<?php
use App\Http\Controllers\CategoryController;

// Category
Route::prefix('categories')->name('categories')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->middleware('remember');
    // Route::get('/create', [CategoryController::class, 'create'])->name('.create');
    Route::post('/', [CategoryController::class, 'store'])->name('.store');
    // Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('.edit');
    // Route::get('/{category}/show', [CategoryController::class, 'show'])->name('.show');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('.update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('.destroy');
    Route::put('/{category}/restore', [CategoryController::class, 'restore'])->name('.restore');
    Route::get('/options', [CategoryController::class, 'options'])->name('.options');

});