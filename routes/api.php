<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/dashboard', [ApiController::class, 'dashboard']);
    
    Route::get('/products', [ApiController::class, 'products']);
    Route::post('/products', [ApiController::class, 'storeProduct']);
    
    Route::get('/customers', [ApiController::class, 'customers']);
    Route::post('/customers', [ApiController::class, 'storeCustomer']);
});
