<?php

use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SpendingController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [FinancialController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::get('/home', [FinancialController::class, 'index'])
    ->middleware(['auth'])
    ->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/home', [FinancialController::class, 'index'])->name('page.layouts.app');
    Route::get('/home/target', [FinancialController::class, 'target'])->name('page.layouts.index');

    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('page.expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('page.expenses.store');
    Route::delete('/expenses/{id}', [FinancialController::class, 'destroy'])->name('expenses.destroy');
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->name('page.expenses.update');

    Route::get('/budgets/create', [BudgetController::class, 'index'])->name('page.budgets.index');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('page.budgets.store');
    Route::get('/budgets/{id}/edit', [BudgetController::class, 'edit'])->name('page.budgets.edit');
    Route::put('/budgets/{id}', [BudgetController::class, 'update'])->name('page.budgets.update');
    Route::delete('/budgets/{id}', [BudgetController::class, 'destroy'])->name('page.budgets.destroy');

    Route::get('/report', [ReportController::class, 'index'])->name('page.reports.monthly');
    Route::get('/reports/yearly', [ReportController::class, 'yearly'])->name('page.reports.yearly');

});


require __DIR__.'/auth.php';
