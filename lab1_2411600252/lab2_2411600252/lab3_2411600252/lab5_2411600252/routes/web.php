<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Lab 5: Hardware Inventory Management System
|--------------------------------------------------------------------------
*/

// Guests land on the themed login page (Breeze provides the "login" route).
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CSV export must be registered before the resource route's {product} binding
    // so that "export" is never mistaken for a product id/slug.
    Route::get('/products/export/csv', [ProductController::class, 'export'])->name('products.export');

    Route::resource('products', ProductController::class);

    Route::post('/products/{product}/stock', [ProductController::class, 'adjustStock'])->name('products.stock');
});

require __DIR__ . '/auth.php';
