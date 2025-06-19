<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        //Lấy tháng/năm từ request hoặc mặc định hiện tại
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $formattedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
        $monthYear = "$year-$formattedMonth";
        $userId = Auth::id();

        //Tổng mục tiêu chi tiêu tháng
        $monthlyTarget = Budget::where('user_id', $userId)
            ->where('month_year', $monthYear)
            ->sum('target_amount');

        //Lấy toàn bộ chi tiêu tháng này
        $monthlyExpenses = Expense::with('category')
            ->where('user_id', $userId)
            ->whereMonth('spend_date', $month)
            ->whereYear('spend_date', $year)
            ->get();

        $monthlyTotal = $monthlyExpenses->sum('amount');

        //Tính số dư
        $balance = $monthlyTarget - $monthlyTotal;

        //Danh mục
        $categories = Category::all();

        //Chi tiêu theo danh mục
        $expensesByCategory = $categories->mapWithKeys(function ($category) use ($monthlyExpenses) {
            $total = $monthlyExpenses->where('category_id', $category->id)->sum('amount');
            return [$category->name => $total];
        })->filter(fn($total) => $total > 0);

        //Top 3 danh mục tiêu nhiều nhất
        $topCategories = $expensesByCategory->sortDesc()->take(3);

        //So sánh với tháng trước
        $prevMonthDate = Carbon::createFromDate($year, $month, 1)->subMonth();
        $prevMonthExpenses = Expense::where('user_id', $userId)
            ->whereMonth('spend_date', $prevMonthDate->month)
            ->whereYear('spend_date', $prevMonthDate->year)
            ->get();
        $prevMonthTotal = $prevMonthExpenses->sum('amount');

        $comparePercentage = $prevMonthTotal > 0
            ? (($monthlyTotal - $prevMonthTotal) / $prevMonthTotal) * 100
            : null;

        //Tỷ lệ hoàn thành mục tiêu & cảnh báo
        $completionRate = 0;
        $warning = null;

        if ($monthlyTarget > 0) {
            $completionRate = ($monthlyTotal / $monthlyTarget) * 100;

            if ($completionRate >= 100) {
                $warning = "⚠️ Bạn đã chi tiêu vượt mục tiêu!";
            }
        }

        //Trả ra view
        return view('page.reports.monthly', compact(
            'monthlyTarget',
            'monthlyTotal',
            'balance',
            'expensesByCategory',
            'topCategories',
            'prevMonthTotal',
            'comparePercentage',
            'completionRate',
            'warning',
            'categories',
            'month',
            'year'
        ));
    }

    public function yearly(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $userId = Auth::id();

        //Tổng mục tiêu cả năm
        $yearlyTarget = Budget::where('user_id', $userId)
            ->where('month_year', 'like', "$year%")
            ->sum('target_amount');


        //Tổng chi tiêu cả năm
        $yearlyExpenses = Expense::where('user_id', $userId)
            ->whereYear('spend_date', $year)
            ->get();
        $yearlyTotal = $yearlyExpenses->sum('amount');

        //Số dư
        $balance = $yearlyTarget - $yearlyTotal;

        //Chi tiêu theo tháng
        $monthlyExpenses = [];
        $monthlyExpenses = Expense::where('user_id', $userId)
            ->whereYear('spend_date', $year)
            ->selectRaw('MONTH(spend_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyExpenses = collect(range(1,12))->map(fn($m) => $monthlyExpenses->get($m, 0));


        return view('page.reports.yearly', compact(
            'yearlyTarget',
            'yearlyTotal',
            'balance',
            'monthlyExpenses',
            'year'
        ));
    }

}
