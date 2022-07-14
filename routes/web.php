<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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
Route::prefix('login')->group(function(){
    Route::get('/', [AuthController::class, 'index'])->name('login.index');
    Route::post('/', [AuthController::class, 'doLogin'])->name('login.post');
});
Route::get('logout', [AuthController::class, 'doLogout'])->name('logout.index');

Route::middleware('check.user')->group(function(){
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::prefix('user')->group(function(){
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::get('add', [UserController::class, 'add'])->name('user.add');
        Route::post('/', [UserController::class, 'store'])->name('user.store');
        Route::get('/edit/{slug}', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/{slug}', [UserController::class, 'update'])->name('user.update');
        Route::get('/delete/{slug}', [UserController::class, 'delete'])->name('user.delete');
    });
});