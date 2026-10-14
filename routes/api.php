<?php

use App\Http\Controllers\OperationalApiController;
use App\Http\Controllers\ServiceApiController;
use App\Http\Middleware\AuthenticateApiAccount;
use App\Http\Middleware\ForceApiJson;
use Illuminate\Support\Facades\Route;

Route::middleware([ForceApiJson::class, 'throttle:60,1'])->group(function (): void {
    Route::get('/services', [ServiceApiController::class, 'index']);
    Route::prefix('v1')->name('api.v1.')->group(function (): void {
        Route::apiResource('services', ServiceApiController::class)->only(['index', 'show']);
        Route::middleware(AuthenticateApiAccount::class)->group(function (): void {
            Route::get('/orders', [OperationalApiController::class, 'orders'])->name('orders.index');
            Route::get('/orders/{order}', [OperationalApiController::class, 'order'])->whereNumber('order')->name('orders.show');
            Route::get('/payments', [OperationalApiController::class, 'payments'])->name('payments.index');
            Route::get('/payments/{payment}', [OperationalApiController::class, 'payment'])->whereNumber('payment')->name('payments.show');
        });
    });
});
