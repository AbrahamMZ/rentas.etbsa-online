<?php
use App\Http\Controllers\MachineryMonthlyExpensesController;

Route::prefix('machinery-monthly-expenses')->name('machinery-monthly-expenses')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [MachineryMonthlyExpensesController::class, 'index'])->middleware('remember');
    // Route::get('/create', [MachineryMonthlyExpensesController::class, 'create'])->name('.create');
    Route::post('/', [MachineryMonthlyExpensesController::class, 'store'])->name('.store');
    // Route::get('/{machineryMonthlyExpenses}/edit', [MachineryMonthlyExpensesController::class, 'edit'])->name('.edit');
    // Route::get('/{machineryMonthlyExpenses}/show', [MachineryMonthlyExpensesController::class, 'show'])->name('.show');
    Route::put('/{machineryMonthlyExpenses}', [MachineryMonthlyExpensesController::class, 'update'])->name('.update');
    Route::delete('/{machineryMonthlyExpenses}', [MachineryMonthlyExpensesController::class, 'destroy'])->name('.destroy');
    Route::put('/{machineryMonthlyExpenses}/restore', [MachineryMonthlyExpensesController::class, 'restore'])->name('.restore');
});