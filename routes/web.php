<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
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
    });
    Route::prefix('vendor')->group(function(){
        Route::get('/', [VendorController::class, 'index'])->name('vendor.index');
        Route::get('/list', [VendorController::class, 'getVendors'])->name('vendor.list');
    });
});