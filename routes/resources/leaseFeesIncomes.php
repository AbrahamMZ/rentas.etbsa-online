<?php
use App\Http\Controllers\LeaseFeesController;

Route::prefix('lease-fees')->name('lease_fees')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [LeaseFeesController::class, 'index'])->middleware('remember');
    // Route::get('/create', [LeaseFeesController::class, 'create'])->name('.create');
    Route::post('/', [LeaseFeesController::class, 'store'])->name('.store');
    // Route::get('/{leaseFees}/edit', [LeaseFeesController::class, 'edit'])->name('.edit');
    // Route::get('/{leaseFees}/show', [LeaseFeesController::class, 'show'])->name('.show');
    Route::put('/{leaseFees}', [LeaseFeesController::class, 'update'])->name('.update');
    Route::delete('/{leaseFees}', [LeaseFeesController::class, 'destroy'])->name('.destroy');
    Route::put('/{leaseFees}/restore', [LeaseFeesController::class, 'restore'])->name('.restore');
});