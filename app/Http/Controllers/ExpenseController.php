<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;

class ExpenseController extends Controller
{
    // Trang hiển thị form thêm chi tiêu
    public function create()
{
    $categories = Category::all();

    $today = now()->toDateString(); // Ngày hôm nay
    $currentMonth = now()->format('Y-m'); // Ví dụ: "2025-05"


    return view('page.expenses.create', compact('categories'));
    
}


    // Xử lý lưu khoản chi tiêu mới
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
        ]);

        return redirect()->route('page.expenses.create')->with('success', 'Thêm khoản chi thành công!');
    }
}
