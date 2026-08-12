<?php

use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\ContractPdfController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\PostponementAdminController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

// Customers
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');

// POS / Cashier
Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');

// Installment Payments
Route::post('/installments/{id}/pay', [InstallmentController::class, 'pay'])->name('installments.pay');

// PDF Contracts and Receipts Generation
Route::get('/plans/{id}/contract-pdf', [ContractPdfController::class, 'printContract'])->name('plans.contract_pdf');
Route::get('/installments/{id}/receipt-pdf', [ContractPdfController::class, 'printReceipt'])->name('installments.receipt_pdf');

// Admin Notifications System
Route::get('/admin/api/notifications', [AdminNotificationController::class, 'getNotifications'])->name('admin.notifications.index');
Route::post('/admin/api/notifications/{id}/read', [AdminNotificationController::class, 'markAsRead'])->name('admin.notifications.read');
Route::post('/admin/api/notifications/read-all', [AdminNotificationController::class, 'markAllAsRead'])->name('admin.notifications.read_all');

// Customer Portal (Public token-based access)
Route::get('/c/{token}', [CustomerPortalController::class, 'show'])->name('customer.portal');
Route::post('/c/{token}/postpone', [CustomerPortalController::class, 'requestPostponement'])->name('customer.postpone');

// Postponement Requests Admin Management
Route::get('/admin/postponements', [PostponementAdminController::class, 'index'])->name('admin.postponements.index');
Route::post('/admin/postponements/{id}/approve', [PostponementAdminController::class, 'approve'])->name('admin.postponements.approve');
Route::post('/admin/postponements/{id}/reject', [PostponementAdminController::class, 'reject'])->name('admin.postponements.reject');



