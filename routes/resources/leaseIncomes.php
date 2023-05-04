<?php
use App\Http\Controllers\LeaseIncomesController;

Route::prefix('lease_incomes')->name('lease-incomes')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [LeaseIncomesController::class, 'index'])->middleware('remember');
    // Route::get('/create', [LeaseIncomesController::class, 'create'])->name('.create');
    Route::post('/', [LeaseIncomesController::class, 'store'])->name('.store');
    // Route::get('/{leaseIncomes}/edit', [LeaseIncomesController::class, 'edit'])->name('.edit');
    // Route::get('/{leaseIncomes}/show', [LeaseIncomesController::class, 'show'])->name('.show');
    Route::put('/{leaseIncomes}', [LeaseIncomesController::class, 'update'])->name('.update');
    Route::delete('/{leaseIncomes}', [LeaseIncomesController::class, 'destroy'])->name('.destroy');
    Route::put('/{leaseIncomes}/restore', [LeaseIncomesController::class, 'restore'])->name('.restore');
});