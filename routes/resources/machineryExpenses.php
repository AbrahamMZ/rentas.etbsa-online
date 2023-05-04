<?php
use App\Http\Controllers\MachineryExpenseController;

Route::prefix('machinery-expenses')->name('machinery-expenses')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [MachineryExpenseController::class, 'index'])->middleware('remember');
    // Route::get('/create', [MachineryExpenseController::class, 'create'])->name('.create');
    Route::post('/', [MachineryExpenseController::class, 'store'])->name('.store');
    // Route::get('/{machineryExpense}/edit', [MachineryExpenseController::class, 'edit'])->name('.edit');
    // Route::get('/{machineryExpense}/show', [MachineryExpenseController::class, 'show'])->name('.show');
    Route::put('/{machineryExpense}', [MachineryExpenseController::class, 'update'])->name('.update');
    Route::delete('/{machineryExpense}', [MachineryExpenseController::class, 'destroy'])->name('.destroy');
    Route::put('/{machineryExpense}/restore', [MachineryExpenseController::class, 'restore'])->name('.restore');
});