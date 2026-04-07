<?php

use App\Http\Controllers\Api\StorefrontIntegrationController;
use App\Http\Middleware\VerifyStorefrontApiKey;
use Illuminate\Support\Facades\Route;

Route::prefix('storefront')
    ->middleware(['throttle:api', VerifyStorefrontApiKey::class])
    ->group(function () {
        Route::post('/products', [StorefrontIntegrationController::class, 'storeProduct'])
            ->name('api.storefront.products.store');
        Route::post('/customers', [StorefrontIntegrationController::class, 'storeCustomer'])
            ->name('api.storefront.customers.store');
        Route::post('/orders', [StorefrontIntegrationController::class, 'storeOrder'])
            ->name('api.storefront.orders.store');
    });
