<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Lấy tháng/năm từ request hoặc mặc định là tháng hiện tại
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $formattedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
        $monthYear = "$year-$formattedMonth";

        // Tổng mục tiêu chi tiêu của tháng
        $monthlyTarget = Budget::where('month_year', $monthYear)->sum('target_amount');

        // Tổng chi tiêu trong tháng
        $monthlyExpenses = Expense::with('category')
            ->whereMonth('spend_date', $month)
            ->whereYear('spend_date', $year)
            ->get();

        $monthlyTotal = $monthlyExpenses->sum('amount');

        // Số dư còn lại
        $balance = $monthlyTarget - $monthlyTotal;

        // Chi tiêu theo danh mục
        $expensesByCategory = $monthlyExpenses->groupBy('category.name')->map(function ($group) {
            return $group->sum('amount');
        });

        return view('page.reports.monthly', compact(
            'monthlyTarget',
            'monthlyTotal',
            'balance',
            'expensesByCategory',
            'month',
            'year'
        ));
    }
}
