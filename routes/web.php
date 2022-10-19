<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
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

});
