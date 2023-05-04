<?php
use App\Http\Controllers\FixesCostsController;

// FixesCosts
Route::prefix('fixes-costs')->name('fixes-costs')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [FixesCostsController::class, 'index'])->middleware('remember');
    // Route::get('/create', [FixesCostsController::class, 'create'])->name('.create');
    Route::post('/', [FixesCostsController::class, 'store'])->name('.store');
    // Route::get('/{fixesCosts}/edit', [FixesCostsController::class, 'edit'])->name('.edit');
    // Route::get('/{fixesCosts}/show', [FixesCostsController::class, 'show'])->name('.show');
    Route::put('/{fixesCosts}', [FixesCostsController::class, 'update'])->name('.update');
    Route::delete('/{fixesCosts}', [FixesCostsController::class, 'destroy'])->name('.destroy');
    Route::put('/{fixesCosts}/restore', [FixesCostsController::class, 'restore'])->name('.restore');
});