<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerAuthController;
use App\Models\User;
use App\Http\Controllers\CustomController;

Route::get('/', function () {
    return view('home');
});

// Buyer landing (form-only)
// Buyer dashboard and catalog
Route::get('/buyer', [OrderController::class, 'dashboard']);
Route::get('/buyer/catalog', [OrderController::class, 'buyer']);

// Pesan (order) page for a selected bouquet
Route::get('/pesan/{id}', [\App\Http\Controllers\OrderController::class, 'createFromBouquet']);

// Custom multi-step flow
Route::get('/custom/step/{step}', [CustomController::class, 'step']);
Route::post('/custom/step/{step}', [CustomController::class, 'postStep']);
Route::get('/custom/reset', [CustomController::class, 'reset']);

// Seller: lihat daftar pesanan dan tombol kirim WA
Route::get('/seller/orders', [OrderController::class, 'index']);
Route::get('/seller/orders/{id}/wa', [OrderController::class, 'waLink']);
Route::get('/seller/orders/{id}', [OrderController::class, 'show']);
Route::post('/seller/orders/{id}/status', [OrderController::class, 'updateStatus']);

// Seller catalog & product edit
Route::get('/seller/catalog', [OrderController::class, 'sellerCatalog']);
Route::get('/seller/bouquets/create', [OrderController::class, 'sellerCreateBouquet']);
Route::post('/seller/bouquets', [OrderController::class, 'sellerStoreBouquet']);
Route::get('/seller/bouquets/{id}/edit', [OrderController::class, 'sellerEditBouquet']);
Route::post('/seller/bouquets/{id}/update', [OrderController::class, 'sellerUpdateBouquet']);
Route::get('/seller/product-editor', [OrderController::class, 'productEditor']);
Route::post('/seller/product-editor/save', [OrderController::class, 'saveProductOptions']);
// (product-editor per-step helper pages removed)

// Public: buat pesanan (buyer)
Route::post('/orders', [OrderController::class, 'store']);

// Seller auth + management (previously admin)
Route::get('/seller/login', [SellerAuthController::class, 'showLogin']);
Route::post('/seller/login', [SellerAuthController::class, 'login']);
Route::get('/seller/logout', [SellerAuthController::class, 'logout']);

// Protected seller management routes (use EnsureSeller middleware)
Route::middleware([\App\Http\Middleware\EnsureSeller::class])->group(function () {
    Route::get('/seller/manage', [OrderController::class, 'manageDashboard'])->name('seller.manage');
    Route::get('/seller/manage/orders', [OrderController::class, 'manageIndex']);
    Route::get('/seller/manage/orders/{order_number}', [OrderController::class, 'manageShow']);
    Route::post('/seller/manage/orders/{id}/update', [OrderController::class, 'manageUpdate']);
    Route::post('/seller/manage/orders/{id}/delete', [OrderController::class, 'manageDestroy']);
    // Seller Bouquet CRUD (previously admin bouquets)
    Route::get('/seller/manage/bouquets', [\App\Http\Controllers\AdminBouquetController::class, 'index']);
    Route::get('/seller/manage/bouquets/create', [\App\Http\Controllers\AdminBouquetController::class, 'create']);
    Route::post('/seller/manage/bouquets', [\App\Http\Controllers\AdminBouquetController::class, 'store']);
    Route::get('/seller/manage/bouquets/{id}/edit', [\App\Http\Controllers\AdminBouquetController::class, 'edit']);
    Route::post('/seller/manage/bouquets/{id}/update', [\App\Http\Controllers\AdminBouquetController::class, 'update']);
    Route::post('/seller/manage/bouquets/{id}/delete', [\App\Http\Controllers\AdminBouquetController::class, 'destroy']);
});
