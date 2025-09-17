<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\CashierController;
use App\Http\Controllers\Api\ComboController;
use App\Http\Controllers\Api\EmployeesController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use Illuminate\Support\Facades\Route;

// Rotas de autenticação
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/register', [LoginController::class, 'register'])->name('register');

// Rotas de logout protegidas
Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Rotas públicas (leitura)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/employees', [EmployeesController::class, 'index']);
Route::get('/employees/{employee}', [EmployeesController::class, 'show']);
Route::get('/combos', [ComboController::class, 'index']);
Route::get('/combos/{combo}', [ComboController::class, 'show']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function (): void {
    // Gestão de produtos
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Gestão de funcionários
    Route::post('/employees', [EmployeesController::class, 'store']);
    Route::put('/employees/{employee}', [EmployeesController::class, 'update']);
    Route::delete('/employees/{employee}', [EmployeesController::class, 'destroy']);

    // Gestão de combos
    Route::post('/combos', [ComboController::class, 'store']);
    Route::put('/combos/{combo}', [ComboController::class, 'update']);
    Route::delete('/combos/{combo}', [ComboController::class, 'destroy']);

    // Gestão de caixa
    Route::get('/cashier/opened', [CashierController::class, 'cashierOpened']);
    Route::post('/cashier/open', [CashierController::class, 'openCashier']);
    Route::post('/cashier/close', [CashierController::class, 'closeCashier']);

    // Vendas
    Route::post('/sales', [SaleController::class, 'store']);
});
