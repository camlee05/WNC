<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Category;

class BudgetController extends Controller
{
    private function getIconMap()
    {
        return [
            'Ăn uống'   => '<img src="' . asset('img/burger.png') . '" alt="Ăn uống" style="width:55px;">',
            'Du lịch'   => '<img src="' . asset('img/travel-bag.png') . '" alt="Du lịch" style="width:55px;">',
            'Mua sắm'   => '<img src="' . asset('img/shopping-cart.png') . '" alt="Mua sắm" style="width:55px;">',
            'Học tập'   => '<img src="' . asset('img/school-material.png') . '" alt="Học tập" style="width:55px;">',
            'Giao thông'=> '<img src="' . asset('img/vehicles.png') . '" alt="Giao thông" style="width:55px;">',
            'Sức khỏe'  => '<img src="' . asset('img/medicine.png') . '" alt="Sức khỏe" style="width:55px;">',
            'Điện thoại'=> '<img src="' . asset('img/app.png') . '" alt="Điện thoại" style="width:55px;">',
            'Thú cưng'  => '<img src="' . asset('img/pet-food.png') . '" alt="Thú cưng" style="width:55px;">',
            'Xã hội'    => '<img src="' . asset('img/ancestors.png') . '" alt="Xã hội" style="width:55px;">',
            'Khác'      => '<img src="' . asset('img/question-mark.png') . '" alt="Khác" style="width:55px;">',
            'Nhà ở'     => '<img src="' . asset('img/house.png') . '" alt="Nhà ở" style="width:55px;">',
            'Giải trí'  => '<img src="' . asset('img/online-gaming.png') . '" alt="Giải trí" style="width:55px;">',
        ];
    }
    public function index(Request $request)
    {
        $categories = Category::all();
        $budgets = Budget::with('category')
         ->where('user_id', auth()->id())
         ->orderBy('month_year', 'desc')
         ->get();
        // Tính tổng mục tiêu theo từng tháng
        $totalBudgetsByMonth = Budget::selectRaw('month_year, SUM(target_amount) as total')
         ->where('user_id', auth()->id())
        ->groupBy('month_year')
        ->orderBy('month_year', 'desc')
        ->pluck('total', 'month_year'); // trả về dạng: ['2025-05' => 3000000, '2025-04' => 2000000]
        $iconMap = $this->getIconMap();
        // Thêm key 'icon' vào từng category
        foreach ($categories as $category) {
        $category->icon = $iconMap[$category->name] ?? 'default.png';
        }
        $budget = null;
        return view('page.budgets.index', compact('categories', 'budgets','totalBudgetsByMonth', 'budget'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month_year' => 'required|date_format:Y-m',
            'target_amount' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'note' => 'nullable|string|max:255',
        ]);

        Budget::updateOrCreate(
            [
                'user_id'     => auth()->id(),
                'month_year' => $request->month_year,
                'category_id' => $request->category_id,
            ],
            [
                'target_amount' => $request->target_amount,
                'note' => $request->note,
            ]
        );

        return redirect()->route('page.budgets.index')->with('success', 'Đặt mục tiêu thành công!');
    }
    
    public function destroy($id)
    {
        $budget = Budget::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();
        $budget->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function edit($id) {
        $budget = Budget::findOrFail($id);
        $categories = Category::all();
        $budgets = Budget::with('category')
            ->where('user_id', auth()->id())
            ->orderBy('month_year', 'desc')
            ->get();
        $totalBudgetsByMonth = Budget::selectRaw('month_year, SUM(target_amount) as total')
            ->where('user_id', auth()->id())
            ->groupBy('month_year')
            ->orderBy('month_year', 'desc')
            ->pluck('total', 'month_year');
        // Thêm icon
        $iconMap = $this->getIconMap();
        foreach ($categories as $category) {
            $category->icon = $iconMap[$category->name] ?? 'default.png';
        }

        return view('page.budgets.index', compact(
            'categories',
            'budgets',
            'totalBudgetsByMonth',
            'budget'
        ));
        }
    public function update(Request $request, $id)
    {
        // Tìm budget theo ID và user
        $budget = Budget::where('id', $id)
                        ->where('user_id', auth()->id())
                        ->firstOrFail();

        // Validate dữ liệu
        $validatedData = $request->validate([
        'month_year' => 'required|date_format:Y-m',
        'target_amount' => 'required|integer|min:1',
        'category_id' => 'required|exists:categories,id',
        'note' => 'nullable|string|max:255',
        ]);
        //Update
        $budget->update($validatedData);
        return redirect()->route('page.budgets.index')
                        ->with('success', 'Cập nhật mục tiêu thành công!');
    }

}