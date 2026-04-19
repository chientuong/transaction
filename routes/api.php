<?php

use Illuminate\Support\Facades\Route;
use Source\Domain\Transaction\Presentation\API\Controllers\BankAccountQueryController;
use Source\Domain\Transaction\Presentation\API\Controllers\PaymentPrefixQueryController;
use Source\Domain\Transaction\Presentation\API\Controllers\TransactionCommandController;
use App\Http\Middleware\VerifySystemToken;
use Source\Domain\Transaction\Presentation\API\Controllers\SepayWebhookController;

$appService = env('APP_SERVICE', 'all');

if (in_array($appService, ['all', 'api'])) {
    Route::middleware(VerifySystemToken::class)->group(function () {
        // Queries
        Route::get('/bank-accounts/active', [BankAccountQueryController::class, 'getActive'])->name('api.bank-accounts.active');
        Route::get('/payment-prefixes/active', [PaymentPrefixQueryController::class, 'getActive'])->name('api.payment-prefixes.active');
        
        // Commands
        Route::post('/transactions', [TransactionCommandController::class, 'store'])->name('api.transactions.store');
    });

    // SePay Webhook (Public with custom auth)
    Route::post('/sepay-webhook', [SepayWebhookController::class, 'handle'])->name('api.sepay-webhook');
}
