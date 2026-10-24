<?php

use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:payment-webhook')->group(function () {
    Route::post('webhooks/payment/{gateway}', PaymentWebhookController::class)
        ->where('gateway', '.+')
        ->name('webhooks.payment');
});
