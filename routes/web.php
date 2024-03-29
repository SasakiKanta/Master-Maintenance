<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\UsersController;

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

Auth::routes();

Route::middleware(['auth'])->group(function () {

    // ホーム
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // 利用者マスタ
    Route::prefix("users")->group(function () {
        Route::get('', [UsersController::class, 'index'])->name('users.index');
        Route::get('init', [UsersController::class, 'init'])->name('users');
        Route::get('entry', [UsersController::class, 'entry'])->name('users.entry');
        Route::get('{id}', [UsersController::class, 'edit'])->name('users.edit');
        Route::put('{id?}', [UsersController::class, 'insert'])->name('users.insert');
        Route::post('{id}', [UsersController::class, 'update'])->name('users.update');
        Route::delete('{id}', [UsersController::class, 'delete'])->name('users.delete');
        Route::post('search', [UsersController::class, 'search'])->name('users.search');
        Route::get('search', [UsersController::class, 'paging'])->name('users.paging');
    });

    //取引先マスタ
    Route::prefix("suppliers")->group(function () {
        Route::get('', [SuppliersController::class, 'index'])->name('suppliers.index');
        Route::get('init', [SuppliersController::class, 'init'])->name('suppliers');
        Route::get('entry', [SuppliersController::class, 'entry'])->name('suppliers.entry');
        Route::get('{id}', [SuppliersController::class, 'edit'])->name('suppliers.edit');
        Route::put('{id?}', [SuppliersController::class, 'insert'])->name('suppliers.insert');
        Route::post('{id}', [SuppliersController::class, 'update'])->name('suppliers.update');
        Route::delete('{id}', [SuppliersController::class, 'delete'])->name('suppliers.delete');
        Route::post('search', [SuppliersController::class, 'search'])->name('suppliers.search');
        Route::get('search', [SuppliersController::class, 'paging'])->name('suppliers.paging');
    });

    //顧客マスタ
    Route::prefix("customers")->group(function () {
        Route::get('', [CustomersController::class, 'index'])->name('customers.index');
        Route::get('init', [CustomersController::class, 'init'])->name('customers');
        Route::get('entry', [CustomersController::class, 'entry'])->name('customers.entry');
        Route::get('{id}', [CustomersController::class, 'edit'])->name('customers.edit');
        Route::put('{id?}', [CustomersController::class, 'insert'])->name('customers.insert');
        Route::post('{id}', [CustomersController::class, 'update'])->name('customers.update');
        Route::delete('{id}', [CustomersController::class, 'delete'])->name('customers.delete');
        Route::post('search', [CustomersController::class, 'search'])->name('customers.search');
        Route::get('search', [CustomersController::class, 'paging'])->name('customers.paging');
        Route::get('csv', [CustomersController::class, 'csv'])->name('customers.csv');
        Route::get('zip', [CustomersController::class, 'zip'])->name('customers.zip');
        Route::post('upload', [CustomersController::class, 'upload'])->name('customers.upload');
        Route::get('errorCsv', [CustomersController::class, 'errorCsv'])->name('customers.errorCsv');
    });
});
