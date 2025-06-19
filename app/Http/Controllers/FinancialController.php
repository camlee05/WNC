<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;

class FinancialController extends Controller
{
    private $icons = [
        'Ăn uống'    => 'img/burger.png',
        'Du lịch'    => 'img/travel-bag.png',
        'Mua sắm'    => 'img/shopping-cart.png',
        'Học tập'    => 'img/school-material.png',
        'Giao thông' => 'img/vehicles.png',
        'Sức khỏe'   => 'img/medicine.png',
        'Điện thoại' => 'img/app.png',
        'Thú cưng'   => 'img/pet-food.png',
        'Xã hội'     => 'img/ancestors.png',
        'Khác'       => 'img/question-mark.png',
        'Nhà ở'      => 'img/house.png',
        'Giải trí'   => 'img/online-gaming.png',
        ];

    private function buildCategoryEmojis()
    {
        $result = [];
        foreach ($this->icons as $name => $path) {
            $url = asset($path);
            $result[$name] = '<img src="' . $url . '" alt="' . $name . '" style="width:55px;">';
        }
        return $result;
    }

    private function getCommonData($month, $year)
    {
        $monthlyExpenses = Expense::with('category')
            ->where('user_id', auth()->id())
            ->whereMonth('spend_date', $month)
            ->whereYear('spend_date', $year)
            ->orderBy('spend_date', 'desc')
            ->get();

        $monthlyTotal = $monthlyExpenses->sum('amount');

        $today = Carbon::today()->toDateString();
        $dailyTotal = Expense::whereDate('spend_date', $today)
            ->where('user_id', auth()->id())
            ->sum('amount');

        $dailyTotals = Expense::whereMonth('spend_date', $month)
            ->whereYear('spend_date', $year)
            ->where('user_id', auth()->id())
            ->selectRaw('DATE(spend_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $monthYear = sprintf("%04d-%02d", $year, $month);
        $monthlyTarget = Budget::where('month_year', $monthYear)
            ->where('user_id', auth()->id())
            ->sum('target_amount');

        $balance = $monthlyTarget - $monthlyTotal;

        return compact(
            'monthlyExpenses',
            'monthlyTotal',
            'dailyTotal',
            'dailyTotals',
            'monthlyTarget',
            'balance'
        );
    }

    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $data = $this->getCommonData($month, $year);
        $categoryEmojis = $this->buildCategoryEmojis();

        return view('page.layouts.app', array_merge($data, compact('categoryEmojis')));
    }

   public function target(Request $request)
    {
        // Lấy tháng-năm từ request hoặc mặc định hiện tại
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Ghép thành chuỗi "YYYY-MM"
        $monthYear = sprintf("%04d-%02d", $year, $month);

        // Dữ liệu chung (tổng chi, số dư,...)
        $data = $this->getCommonData($month, $year);

        // Lấy các mục tiêu của tháng này, đúng user
        $targets = Budget::with('category')
            ->where('month_year', $monthYear)
            ->where('user_id', auth()->id()) // ✅ Luôn có ()
            ->get();

        // Gán emoji
        $categoryEmojis = $this->buildCategoryEmojis();

        // Trả về view với dữ liệu đầy đủ
        return view('page.layouts.target', array_merge(
            $data,
            compact('targets', 'categoryEmojis', 'monthYear')
        ));
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
