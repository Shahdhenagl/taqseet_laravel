<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

// Customers
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

// POS / Cashier
Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');

// Installment Payments
Route::post('/installments/{id}/pay', [InstallmentController::class, 'pay'])->name('installments.pay');
