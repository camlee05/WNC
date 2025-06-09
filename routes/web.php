<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SpendingController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ExpenseController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('page.expenses.create');
Route::post('/expenses', [ExpenseController::class, 'store'])->name('page.expenses.store');

Route::get('/budgets', [BudgetController::class, 'index'])->name('page.budgets.index');
Route::post('/budgets', [BudgetController::class, 'store'])->name('page.budgets.store');

Route::get('/home', [SpendingController::class, 'index'])->name('page.layouts.app');

Route::get('/report', [ReportController::class, 'index'])->name('page.reports.monthly');



Route::get('/report/yearly', function () {
    return view('page.reports.yearly');
});

require __DIR__.'/auth.php';
