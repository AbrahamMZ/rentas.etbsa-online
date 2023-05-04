<?php
use App\Http\Controllers\ExpenseCatalogController;

Route::prefix('expenses')->name('expenses')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [ExpenseCatalogController::class, 'index'])->middleware('remember');
    // Route::get('/create', [ExpenseCatalogController::class, 'create'])->name('.create');
    Route::post('/', [ExpenseCatalogController::class, 'store'])->name('.store');
    // Route::get('/{expense}/edit', [ExpenseCatalogController::class, 'edit'])->name('.edit');
    // Route::get('/{expense}/show', [ExpenseCatalogController::class, 'show'])->name('.show');
    Route::put('/{expense}', [ExpenseCatalogController::class, 'update'])->name('.update');
    Route::delete('/{expense}', [ExpenseCatalogController::class, 'destroy'])->name('.destroy');
    Route::put('/{expense}/restore', [ExpenseCatalogController::class, 'restore'])->name('.restore');
    Route::get('/options', [ExpenseCatalogController::class, 'options'])->name('.options');

});