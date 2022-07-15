<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CategoryController;
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
        Route::get('/list', [UserController::class, 'get'])->name('user.list');
        Route::get('add', [UserController::class, 'add'])->name('user.add');
        Route::post('/', [UserController::class, 'store'])->name('user.store');
        Route::get('/edit/{slug}', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/{slug}', [UserController::class, 'update'])->name('user.update');
        Route::get('/delete/{slug}', [UserController::class, 'delete'])->name('user.delete');
    });
    Route::prefix('vendor')->group(function(){
        Route::get('/', [VendorController::class, 'index'])->name('vendor.index');
        Route::get('/list', [VendorController::class, 'get'])->name('vendor.list');
        Route::get('add', [VendorController::class, 'add'])->name('vendor.add');
        Route::post('/', [VendorController::class, 'store'])->name('vendor.store');
        Route::get('/edit/{slug}', [VendorController::class, 'edit'])->name('vendor.edit');
        Route::put('/{slug}', [VendorController::class, 'update'])->name('vendor.update');
        Route::get('/delete/{slug}', [VendorController::class, 'delete'])->name('vendor.delete');
    });
    Route::prefix('category')->group(function(){
        Route::get('/', [CategoryController::class, 'index'])->name('category.index');
        Route::get('/list', [CategoryController::class, 'get'])->name('category.list');
        Route::get('add', [CategoryController::class, 'add'])->name('category.add');
        Route::post('/', [CategoryController::class, 'store'])->name('category.store');
        Route::get('/edit/{slug}', [CategoryController::class, 'edit'])->name('category.edit');
        Route::put('/{slug}', [CategoryController::class, 'update'])->name('category.update');
        Route::get('/delete/{slug}', [CategoryController::class, 'delete'])->name('category.delete');
    });
});