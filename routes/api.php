<?php

use App\Http\Controllers\client\v1\PaymentController;
use \App\Http\Controllers\admin\v1\PaymentController as AdminPaymentController;
use App\Http\Controllers\client\v1\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('client')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::prefix('payments')->group(function () {
            Route::post('/create', [PaymentController::class,'create'])->name('payment.create.v1');
            Route::get('/{payment}', [PaymentController::class,'payment'])->name('payments.show.v1');
            Route::post('/reverse', [PaymentController::class,'reverse'])->name('transaction.reverse.v1');
        });
        Route::prefix('transactions')->group(function () {
            Route::get('/verify', [TransactionController::class,'verify'])->name('transaction.verify.v1');
        });
    });
});
Route::prefix('admin')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::prefix('payments')->group(function () {
            Route::get('/', [AdminPaymentController::class,'payments'])->name('payments.list.v1');
        });
    });
});
