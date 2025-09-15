<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\CashierController;
use App\Http\Controllers\Api\ComboController;
use App\Http\Controllers\Api\EmployeesController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/register', [LoginController::class, 'register'])->name('register');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/employees', [EmployeesController::class, 'index']);
Route::get('/employees/{employee}', [EmployeesController::class, 'show']);
Route::get('/combos', [ComboController::class, 'index']);
Route::get('/combos/{combo}', [ComboController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    Route::post('/employees', [EmployeesController::class, 'store']);
    Route::put('/employees/{employee}', [EmployeesController::class, 'update']);
    Route::delete('/employees/{employee}', [EmployeesController::class, 'destroy']);

    Route::post('/combos', [ComboController::class, 'store']);
    Route::put('/combos/{combo}', [ComboController::class, 'update']);
    Route::delete('/combos/{combo}', [ComboController::class, 'destroy']);

    Route::post('/cashier/open', [CashierController::class, 'openCashier']);
    Route::get('/cashier/open', [CashierController::class, 'cashierOpen']);
    Route::post('/cashier/close', [CashierController::class, 'closeCashier']);
});
