<?php
use App\Http\Controllers\MachineryServiceExpensesController;

Route::prefix('machinery-services-expenses')->name('machinery-services-expenses')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [MachineryServiceExpensesController::class, 'index'])->middleware('remember');
    // Route::get('/create', [MachineryServiceExpensesController::class, 'create'])->name('.create');
    Route::post('/', [MachineryServiceExpensesController::class, 'store'])->name('.store');
    // Route::get('/{machineryServiceExpenses}/edit', [MachineryServiceExpensesController::class, 'edit'])->name('.edit');
    // Route::get('/{machineryServiceExpenses}/show', [MachineryServiceExpensesController::class, 'show'])->name('.show');
    Route::put('/{machineryServiceExpenses}', [MachineryServiceExpensesController::class, 'update'])->name('.update');
    Route::delete('/{machineryServiceExpenses}', [MachineryServiceExpensesController::class, 'destroy'])->name('.destroy');
    Route::put('/{machineryServiceExpenses}/restore', [MachineryServiceExpensesController::class, 'restore'])->name('.restore');
});