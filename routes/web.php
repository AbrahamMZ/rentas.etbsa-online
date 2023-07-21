<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExpedientController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\ReportsController;

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

Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});
