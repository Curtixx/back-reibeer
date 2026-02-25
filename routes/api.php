<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\CashierController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ComboController;
use App\Http\Controllers\Api\DashboardController;
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
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function (): void {
    Route::prefix('dashboard')->group(function (): void {
        Route::get('/', [DashboardController::class, 'index']);
    });

    // Gestão de produtos
    Route::prefix('products')->group(function (): void {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{product}', [ProductController::class, 'update']);
        Route::delete('/{product}', [ProductController::class, 'destroy']);
    });

    // Gestão de funcionários
    Route::prefix('employees')->group(function (): void {
        Route::post('/', [EmployeesController::class, 'store']);
        Route::put('/{employee}', [EmployeesController::class, 'update']);
        Route::delete('/{employee}', [EmployeesController::class, 'destroy']);
    });

    // Gestão de combos
    Route::prefix('combos')->group(function (): void {
        Route::post('/', [ComboController::class, 'store']);
        Route::put('/{combo}', [ComboController::class, 'update']);
        Route::delete('/{combo}', [ComboController::class, 'destroy']);
    });

    // Gestão de categorias
    Route::prefix('categories')->group(function (): void {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
    });

    // Gestão de comandas
    Route::prefix('orders')->group(function (): void {
        Route::post('/', [OrderController::class, 'store']);
        Route::put('/{order}', [OrderController::class, 'update']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
        Route::post('/{order}/add-products', [OrderController::class, 'addProducts']);
        Route::post('/{order}/remove-products', [OrderController::class, 'removeProducts']);
    });

    // Gestão de caixa
    Route::prefix('cashier')->group(function (): void {
        Route::get('/opened', [CashierController::class, 'cashierOpened']);
        Route::post('/open', [CashierController::class, 'openCashier']);
        Route::post('/close', [CashierController::class, 'closeCashier']);
    });

    // Estoque
    Route::prefix('stocks')->group(function (): void {
        Route::get('/', [StockController::class, 'index']);
        Route::get('/{stock}', [StockController::class, 'show']);
        Route::post('/', [StockController::class, 'store']);
        Route::put('/{stock}', [StockController::class, 'update']);
        Route::put('/update-quantity/{stock}', [StockController::class, 'updateQuantity']);
        Route::delete('/{stock}', [StockController::class, 'destroy']);
    });

    // Vendas
    Route::prefix('sales')->group(function (): void {
        Route::post('/', [SaleController::class, 'store']);
    });
});
