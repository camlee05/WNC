<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    // Trang hiển thị form thêm/sửa chi tiêu
    public function create(Request $request)
    {
        $categories = Category::all(); // hoặc lọc theo user nếu cần

        $iconMap = [
            'Ăn uống'     => '<img src="' . asset('img/burger.png') . '" alt="Ăn uống" style="width:55px;">',
            'Du lịch'     => '<img src="' . asset('img/travel-bag.png') . '" alt="Du lịch" style="width:55px;">',
            'Mua sắm'     => '<img src="' . asset('img/shopping-cart.png') . '" alt="Mua sắm" style="width:55px;">',
            'Học tập'     => '<img src="' . asset('img/school-material.png') . '" alt="Học tập" style="width:55px;">',
            'Giao thông'  => '<img src="' . asset('img/vehicles.png') . '" alt="Giao thông" style="width:55px;">',
            'Sức khỏe'    => '<img src="' . asset('img/medicine.png') . '" alt="Sức khỏe" style="width:55px;">',
            'Điện thoại'  => '<img src="' . asset('img/app.png') . '" alt="Điện thoại" style="width:55px;">',
            'Thú cưng'    => '<img src="' . asset('img/pet-food.png') . '" alt="Thú cưng" style="width:55px;">',
            'Xã hội'      => '<img src="' . asset('img/ancestors.png') . '" alt="Xã hội" style="width:55px;">',
            'Khác'        => '<img src="' . asset('img/question-mark.png') . '" alt="Khác" style="width:55px;">',
            'Nhà ở'       => '<img src="' . asset('img/house.png') . '" alt="Nhà ở" style="width:55px;">',
            'Giải trí'    => '<img src="' . asset('img/online-gaming.png') . '" alt="Giải trí" style="width:55px;">',
        ];

        foreach ($categories as $category) {
        $category->icon = $iconMap[$category->name] ?? $iconMap['Khác'];
        }

        $editExpense = null;
        if ($request->has('edit_id')) {
            $editExpense = Expense::where('id', $request->edit_id)
                                  ->where('user_id', Auth::id())
                                  ->firstOrFail();
        }

        return view('page.expenses.create', compact('categories', 'editExpense'));
    }

    // Lưu khoản chi tiêu mới
    public function store(Request $request)
    {
        $request->validate([
            'spend_date' => 'required|date',
            'amount' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'note' => 'nullable|string|max:255',
        ]);

        Expense::create([
            'spend_date' => $request->spend_date,
            'amount' => $request->amount,
            'category_id' => $request->category_id,
            'note' => $request->note,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('page.expenses.create')->with('success', 'Thêm khoản chi thành công!');
    }

    // Cập nhật khoản chi tiêu
    public function update(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'spend_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
        ]);

        $spending = Expense::where('id', $id)
                           ->where('user_id', Auth::id())
                           ->firstOrFail();

        $spending->update([
            'note' => $request->note,
            'amount' => $request->amount,
            'spend_date' => $request->spend_date,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('page.expenses.create')->with('success', 'Chi tiêu đã được cập nhật.');
    }
}
