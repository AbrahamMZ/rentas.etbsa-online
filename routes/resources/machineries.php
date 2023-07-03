<?php
use App\Http\Controllers\MachineryController;
use App\Http\Controllers\MachineryImageController;

Route::prefix('machineries')->name('machineries')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [MachineryController::class, 'index'])->middleware('remember');
    Route::get('/create', [MachineryController::class, 'create'])->name('.create');
    Route::post('/', [MachineryController::class, 'store'])->name('.store');
    Route::get('/{machinery}/edit', [MachineryController::class, 'edit'])->name('.edit');
    Route::get('/{machinery}/show', [MachineryController::class, 'show'])->name('.show');
    Route::put('/{machinery}', [MachineryController::class, 'update'])->name('.update');
    Route::delete('/{machinery}', [MachineryController::class, 'destroy'])->name('.destroy');
    Route::put('/{machinery}/restore', [MachineryController::class, 'restore'])->name('.restore');

    Route::put('/{machinery}/updateMachineryFixesCosts', [MachineryController::class, 'updateMachineryFixesCosts'])->name('.updateMachineryFixesCosts');

});