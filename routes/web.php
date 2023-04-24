<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExpedientController;
use App\Http\Controllers\ExpenseCatalogController;
use App\Http\Controllers\FixesCostsController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\MachineryController;
use App\Http\Controllers\MachineryExpenseController;
use App\Http\Controllers\MachineryServiceExpensesController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth

Route::get('login', [LoginController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [LoginController::class, 'login'])
    ->name('login.attempt')
    ->middleware('guest');

Route::post('logout', [LoginController::class, 'logout'])
    ->name('logout');

Route::get('simulador', [DashboardController::class, 'showSimulator'])
    ->name('simulator')
    ->middleware('guest');


// Dashboard

// Route::get('/', [DashboardController::class, 'index'])
//     ->name('dashboard')
//     ->middleware('auth');
Route::get('/', [ExpedientController::class, 'index'])->middleware('auth');

// Reports
Route::get('reports', [ReportsController::class, 'index'])
    ->name('reports')
    ->middleware('auth');


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



// Requirements
Route::prefix('requirements')->name('requirements')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [RequirementController::class, 'index'])->middleware('remember');
    Route::get('/create', [RequirementController::class, 'create'])->name('.create');
    Route::post('/', [RequirementController::class, 'store'])->name('.store');
    Route::get('/{requirement}/edit', [RequirementController::class, 'edit'])->name('.edit');
    Route::put('/{requirement}', [RequirementController::class, 'update'])->name('.update');
    Route::delete('/{requirement}', [RequirementController::class, 'destroy'])->name('.destroy');
    Route::put('/{requirement}/restore', [RequirementController::class, 'restore'])->name('.restore');
});
// Templates
Route::prefix('templates')->name('templates')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [TemplateController::class, 'index'])->middleware('remember');
    Route::get('/create', [TemplateController::class, 'create'])->name('.create');
    Route::post('/', [TemplateController::class, 'store'])->name('.store');
    Route::get('/{template}/edit', [TemplateController::class, 'edit'])->name('.edit');
    Route::put('/{template}', [TemplateController::class, 'update'])->name('.update');
    Route::delete('/{template}', [TemplateController::class, 'destroy'])->name('.destroy');
    Route::put('/{template}/restore', [TemplateController::class, 'restore'])->name('.restore');
});

// Expedients
Route::prefix('expedients')->name('expedients')->middleware('auth')->group(function () {
    Route::get('/', [ExpedientController::class, 'index'])->middleware('remember');
    Route::get('/{expedient}/show', [ExpedientController::class, 'show'])->name('.show');
    Route::get('/create', [ExpedientController::class, 'create'])->name('.create');
    Route::post('/', [ExpedientController::class, 'store'])->name('.store');
    Route::get('/{expedient}/documents', [ExpedientController::class, 'documents'])->name('.documents');
    Route::get('/{expedient}/edit', [ExpedientController::class, 'edit'])->name('.edit');
    Route::put('/{expedient}', [ExpedientController::class, 'update'])->name('.update');
    Route::delete('/{expedient}', [ExpedientController::class, 'destroy'])->name('.destroy');
    Route::put('/{expedient}/restore', [ExpedientController::class, 'restore'])->name('.restore');
    Route::put('/{expedient}/addRequirement', [ExpedientController::class, 'addRequirement'])->name('.addRequirement');
});

// Documents
Route::prefix('documents')->name('documents')->middleware('auth')->group(function () {
    Route::get('/', [DocumentController::class, 'index'])->middleware('remember');
    Route::put('/{document}/update', [DocumentController::class, 'update'])->name('.update');
    Route::get('/{document}/downloadFilesZip', [DocumentController::class, 'downloadFilesZip'])->name('.downloadFilesZip');
    Route::put('/{document}/uploadFiles', [DocumentController::class, 'uploadFiles'])->name('.uploadFiles');
    Route::delete('/{file}/deleteFile', [DocumentController::class, 'deleteFile'])->name('.deleteFile');
});
// Machinery
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
// Expenses Catalog
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
// Machinery Services Expenses
Route::prefix('machinery-services-expenses')->name('machinery-services-expenses')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [MachineryServiceExpensesController::class, 'index'])->middleware('remember');
    // Route::get('/create', [MachineryServiceExpensesController::class, 'create'])->name('.create');
    Route::post('/', [MachineryServiceExpensesController::class, 'store'])->name('.store');
    // Route::get('/{machineryServiceExpenses}/edit', [MachineryServiceExpensesController::class, 'edit'])->name('.edit');
    // Route::get('/{machineryServiceExpenses}/show', [MachineryServiceExpensesController::class, 'show'])->name('.show');
    Route::put('/{machineryServiceExpenses}', [MachineryServiceExpensesController::class, 'update'])->name('.update');
    Route::delete('/{machineryServiceExpenses}', [MachineryServiceExpensesController::class, 'destroy'])->name('.destroy');
    Route::put('/{machineryServiceExpenses}/restore', [MachineryServiceExpensesController::class, 'restore'])->name('.restore');
});
// Machinery Expenses
Route::prefix('machinery-expenses')->name('machinery-expenses')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [MachineryExpenseController::class, 'index'])->middleware('remember');
    // Route::get('/create', [MachineryExpenseController::class, 'create'])->name('.create');
    Route::post('/', [MachineryExpenseController::class, 'store'])->name('.store');
    // Route::get('/{machineryExpense}/edit', [MachineryExpenseController::class, 'edit'])->name('.edit');
    // Route::get('/{machineryExpense}/show', [MachineryExpenseController::class, 'show'])->name('.show');
    Route::put('/{machineryExpense}', [MachineryExpenseController::class, 'update'])->name('.update');
    Route::delete('/{machineryExpense}', [MachineryExpenseController::class, 'destroy'])->name('.destroy');
    Route::put('/{machineryExpense}/restore', [MachineryExpenseController::class, 'restore'])->name('.restore');
});



// Images

Route::get('/img/{path}', [ImagesController::class, 'show'])->where('path', '.*');
Route::get('/files/{path}', [ImagesController::class, 'files'])->where('path', '.*');

// 500 error

// Route::get('500', function () {
//     // Force debug mode for this endpoint in the demo environment
//     if (App::environment('demo')) {
//         Config::set('app.debug', true);
//     }

//     echo $fail;
// });
