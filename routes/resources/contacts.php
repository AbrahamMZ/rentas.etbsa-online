<?php
use App\Http\Controllers\ContactsController;


// Contacts
Route::prefix('contacts')->name('contacts')->middleware(['auth'])->group(function () {

    Route::get('/', [ContactsController::class, 'index'])->middleware('remember');
    Route::get('/create', [ContactsController::class, 'create'])->name('.create');
    Route::post('', [ContactsController::class, 'store'])->name('.store');
    Route::get('/{contact}/edit', [ContactsController::class, 'edit'])->name('.edit');
    Route::put('/{contact}', [ContactsController::class, 'update'])->name('.update');
    Route::delete('/{contact}', [ContactsController::class, 'destroy'])->name('.destroy');
    Route::put('/{contact}/restore', [ContactsController::class, 'restore'])->name('.restore');

});