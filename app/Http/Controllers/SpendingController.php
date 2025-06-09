<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Budget;
use Carbon\Carbon;

class SpendingController extends Controller
{
    public function index(Request $request)
    {
$categoryColors = [
    'Ăn uống'     => '#007bff',  // xanh dương chuẩn (Bootstrap primary)
    'Du lịch'     => '#dc3545',  // đỏ tươi (Bootstrap danger)
    'Mua sắm'     => '#ffc107',  // vàng sáng (Bootstrap warning)
    'Học tập'     => '#28a745',  // xanh lá tươi (Bootstrap success)
    'Giao thông'  => '#6f42c1',  // tím đậm (Bootstrap purple)
    'Sức khỏe'    => '#fd7e14',  // cam tươi (Bootstrap orange)
    'Điện thoại'  => '#20c997',  // xanh ngọc (Bootstrap teal)
    'Thú cưng'    => '#e83e8c',  // hồng sáng (Bootstrap pink)
    'Xã hội'      => '#17a2b8',  // xanh biển (Bootstrap info)
    'Khác'       => '#343a40',  // xám đen (Bootstrap dark)
    'Nhà ở'      => '#6610f2',  // tím sáng (Bootstrap purple 2)
    'Giải trí'   => '#fd5e53',  // đỏ cam sáng (đỏ san hô)
];

        // Lấy tháng và năm hiện tại hoặc từ request
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Lấy dữ liệu chi tiêu theo tháng và năm
        $monthlyExpenses = Expense::with('category')
            ->whereMonth('spend_date', $month)
            ->whereYear('spend_date', $year)
            ->orderBy('spend_date', 'desc')
            ->get();

        // Tổng chi tiêu trong tháng
        $monthlyTotal = $monthlyExpenses->sum('amount');

        // Tổng chi tiêu hôm nay (theo ngày hiện tại)
        $today = Carbon::today()->toDateString();
        $dailyTotal = Expense::whereDate('spend_date', $today)->sum('amount');

        // Tổng chi tiêu theo từng ngày trong tháng (dùng cho calendar)
        $dailyTotals = Expense::whereMonth('spend_date', $month)
            ->whereYear('spend_date', $year)
            ->selectRaw('DATE(spend_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date'); // key: '2025-05-27', value: 50000

        // Tổng mục tiêu theo tháng
        $formattedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
        $monthYear = "$year-$formattedMonth";
        $monthlyTarget = Budget::where('month_year', $monthYear)->sum('target_amount');

        // Số dư = mục tiêu - chi tiêu
        $balance = $monthlyTarget - $monthlyTotal;

        return view('page.layouts.app', compact(
            'monthlyExpenses',
            'monthlyTotal',
            'monthlyTarget',
            'balance',
            'dailyTotals',
            'dailyTotal', 
            'categoryColors', // thêm biến này để view dùng hiển thị tổng chi tiêu hôm nay
        ));
        
    }
}
