<?php

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

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // 利用者マスタ検索(users)
    Route::prefix("users")->group(function () {
        Route::get('', [App\Http\Controllers\UsersController::class, 'index'])->name('users.index');
        Route::get('entry', [App\Http\Controllers\UsersController::class, 'entry'])->name('users.entry');
        Route::get('{id}', [App\Http\Controllers\UsersController::class, 'edit'])->where('id', '[0-9]+')->name('users.edit');
        Route::post('insert', [App\Http\Controllers\UsersController::class, 'insert'])->name('users.insert');
        Route::post('update', [App\Http\Controllers\UsersController::class, 'update'])->name('users.update');
        Route::post('delete', [App\Http\Controllers\UsersController::class, 'delete'])->name('users.delete');
        Route::match(['get','post'], 'search', [App\Http\Controllers\UsersController::class, 'search'])->name('users.search');
    });
});
