<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerAuthController;
use App\Http\Controllers\BuyerAuthController;
use App\Http\Controllers\CustomController;
use App\Http\Controllers\AdminBouquetController;

/*
|--------------------------------------------------------------------------
| Redirect Root
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/buyer/login');

Route::get('/seller', function () {
    return redirect('/seller/login');
});

/*
|--------------------------------------------------------------------------
| BUYER AUTH
|--------------------------------------------------------------------------
*/
Route::get('/buyer/login', [BuyerAuthController::class, 'showLogin'])->name('buyer.login');
Route::post('/buyer/login', [BuyerAuthController::class, 'login']);
Route::get('/buyer/register', [BuyerAuthController::class, 'showRegister'])->name('buyer.register');
Route::post('/buyer/register', [BuyerAuthController::class, 'register']);
Route::get('/buyer/logout', [BuyerAuthController::class, 'logout'])->name('buyer.logout');

/*
|--------------------------------------------------------------------------
| BUYER PROTECTED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware([\App\Http\Middleware\EnsureBuyer::class])->group(function () {

    // Dashboard & catalog
    Route::get('/buyer', [OrderController::class, 'dashboard'])->name('buyer.dashboard');
    Route::get('/buyer/catalog', [OrderController::class, 'buyer'])->name('buyer.catalog');

    // Order flow
    Route::get('/pesan/{id}', [OrderController::class, 'createFromBouquet'])
        ->name('orders.create');

    Route::post('/orders', [OrderController::class, 'store'])
        ->name('orders.store');

    Route::get('/orders/{id}', [OrderController::class, 'showOrder'])
        ->name('orders.show');

    // 🔥 PAYMENT (POST + NAMED ROUTE)
    Route::post('/orders/{id}/pay', [OrderController::class, 'pay'])
        ->name('orders.pay');

    // Custom bouquet flow
    Route::get('/custom/step/{step}', [CustomController::class, 'step'])
        ->name('custom.step');

    Route::post('/custom/step/{step}', [CustomController::class, 'postStep'])
        ->name('custom.step.post');

    Route::get('/custom/reset', [CustomController::class, 'reset'])
        ->name('custom.reset');
});

/*
|--------------------------------------------------------------------------
| MIDTRANS WEBHOOK
|--------------------------------------------------------------------------
*/
Route::post('/midtrans/notify', [OrderController::class, 'midtransNotify'])
    ->name('midtrans.notify');

/*
|--------------------------------------------------------------------------
| SELLER AUTH
|--------------------------------------------------------------------------
*/
Route::get('/seller/login', [SellerAuthController::class, 'showLogin'])->name('seller.login');
Route::post('/seller/login', [SellerAuthController::class, 'login']);
Route::get('/seller/logout', [SellerAuthController::class, 'logout'])->name('seller.logout');

/*
|--------------------------------------------------------------------------
| SELLER PROTECTED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware([\App\Http\Middleware\EnsureSeller::class])->group(function () {

    // Seller dashboard
    Route::get('/seller/manage', [OrderController::class, 'manageDashboard'])
        ->name('seller.manage');

    // Orders
    Route::get('/seller/manage/orders', [OrderController::class, 'manageIndex'])
        ->name('seller.orders');

    Route::get('/seller/manage/orders/{order_number}', [OrderController::class, 'manageShow'])
        ->name('seller.orders.show');

    Route::post('/seller/manage/orders/{id}/update', [OrderController::class, 'manageUpdate'])
        ->name('seller.orders.update');

    Route::post('/seller/manage/orders/{id}/delete', [OrderController::class, 'manageDestroy'])
        ->name('seller.orders.delete');

    // Bouquet CRUD
    Route::get('/seller/manage/bouquets', [AdminBouquetController::class, 'index'])
        ->name('seller.bouquets');

    Route::get('/seller/manage/bouquets/create', [AdminBouquetController::class, 'create'])
        ->name('seller.bouquets.create');

    Route::post('/seller/manage/bouquets', [AdminBouquetController::class, 'store'])
        ->name('seller.bouquets.store');

    Route::get('/seller/manage/bouquets/{id}/edit', [AdminBouquetController::class, 'edit'])
        ->name('seller.bouquets.edit');

    Route::post('/seller/manage/bouquets/{id}/update', [AdminBouquetController::class, 'update'])
        ->name('seller.bouquets.update');

    Route::post('/seller/manage/bouquets/{id}/delete', [AdminBouquetController::class, 'destroy'])
        ->name('seller.bouquets.delete');
});
