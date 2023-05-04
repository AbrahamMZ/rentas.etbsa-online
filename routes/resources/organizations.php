<?php
use App\Http\Controllers\OrganizationsController;

// Organizations
Route::prefix('organizations')->name('organizations')->middleware(['auth'])->group(function () {

    Route::get('/', [OrganizationsController::class, 'index'])->middleware('remember');
    Route::get('/create', [OrganizationsController::class, 'create'])->name('.create');
    Route::post('', [OrganizationsController::class, 'store'])->name('.store');
    Route::get('/{organization}/edit', [OrganizationsController::class, 'edit'])->name('.edit');
    Route::put('/{organization}', [OrganizationsController::class, 'update'])->name('.update');
    Route::delete('/{organization}', [OrganizationsController::class, 'destroy'])->name('.destroy');
    Route::put('/{organization}/restore', [OrganizationsController::class, 'restore'])->name('.restore');
});