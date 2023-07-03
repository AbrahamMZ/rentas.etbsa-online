<?php
use App\Http\Controllers\MachineryImageController;

Route::prefix('machinery-images')->name('machinery-images')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [MachineryImageController::class, 'index'])->middleware('remember');
    Route::post('/', [MachineryImageController::class, 'store'])->name('.store');
    // Route::put('/{machineryImage}', [MachineryImageController::class, 'update'])->name('.update');
    Route::delete('/{machineryImage}', [MachineryImageController::class, 'destroy'])->name('.destroy');
    // Route::put('/{machineryImage}/restore', [MachineryImageController::class, 'restore'])->name('.restore');
});