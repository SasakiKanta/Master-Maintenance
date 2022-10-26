<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
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

    // 得意先マスタ
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

});
