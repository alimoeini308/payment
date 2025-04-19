<?php

use App\Http\Controllers\client\v1\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('client')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::post('/create', [PaymentController::class,'create'])->name('payment.create.v1');
        Route::get('/verify', [PaymentController::class,'verify'])->name('payment.verify.v1');
    });
});
