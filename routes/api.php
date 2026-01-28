<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\CashierController;
use App\Http\Controllers\Api\ComboController;
use App\Http\Controllers\Api\EmployeesController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\StockController;
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
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{order}', [OrderController::class, 'show']);

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

    // Gestão de comandas
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);

    // Gestão de caixa
    Route::get('/cashier/opened', [CashierController::class, 'cashierOpened']);
    Route::post('/cashier/open', [CashierController::class, 'openCashier']);
    Route::post('/cashier/close', [CashierController::class, 'closeCashier']);

    // Estoque
    Route::get('/stocks', [StockController::class, 'index']);
    Route::get('/stocks/{stock}', [StockController::class, 'show']);
    Route::post('/stocks', [StockController::class, 'store']);
    Route::put('/stocks/{stock}', [StockController::class, 'update']);
    Route::put('/stocks/update-quantity/{stock}', [StockController::class, 'updateQuantity']);
    Route::delete('/stocks/{stock}', [StockController::class, 'destroy']);

    // Vendas
    Route::post('/sales', [SaleController::class, 'store']);
});
