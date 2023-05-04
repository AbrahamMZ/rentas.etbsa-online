<?php
use App\Http\Controllers\UsersController;

// Users
Route::prefix('users')->name('users')->middleware(['auth'])->group(function () {
    Route::get('/', [UsersController::class, 'index'])->middleware('remember');
    Route::get('/create', [UsersController::class, 'create'])->name('.create');
    Route::post('', [UsersController::class, 'store'])->name('.store');
    Route::get('/{user}/edit', [UsersController::class, 'edit'])->name('.edit');
    Route::put('/{user}', [UsersController::class, 'update'])->name('.update');
    Route::delete('/{user}', [UsersController::class, 'destroy'])->name('.destroy');
    Route::put('/{user}/restore', [UsersController::class, 'restore'])->name('.restore');
});