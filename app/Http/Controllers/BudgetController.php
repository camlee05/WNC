<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Category;

class BudgetController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $budgets = Budget::with('category')->orderBy('month_year', 'desc')->get();
        // Tính tổng mục tiêu theo từng tháng
        $totalBudgetsByMonth = Budget::selectRaw('month_year, SUM(target_amount) as total')
        ->groupBy('month_year')
        ->orderBy('month_year', 'desc')
        ->pluck('total', 'month_year'); // trả về dạng: ['2025-05' => 3000000, '2025-04' => 2000000]

        return view('page.budgets.index', compact('categories', 'budgets'));
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
}