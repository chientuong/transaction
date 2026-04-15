<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

use App\Domain\Transaction\Presentation\API\Controllers\BankAccountQueryController;
use App\Domain\Transaction\Presentation\API\Controllers\PaymentPrefixQueryController;
use App\Domain\Transaction\Presentation\API\Controllers\TransactionCommandController;
use App\Http\Middleware\VerifySystemToken;

Route::prefix('api')->middleware(VerifySystemToken::class)->group(function () {
    // Queries
    Route::get('/bank-accounts/active', [BankAccountQueryController::class, 'getActive'])->name('api.bank-accounts.active');
    Route::get('/payment-prefixes/active', [PaymentPrefixQueryController::class, 'getActive'])->name('api.payment-prefixes.active');
    
    // Commands
    Route::post('/transactions', [TransactionCommandController::class, 'store'])->name('api.transactions.store');
});

// SePay Webhook (Public with custom auth)
use App\Domain\Transaction\Presentation\API\Controllers\SepayWebhookController;
Route::post('/api/sepay-webhook', [SepayWebhookController::class, 'handle'])->name('api.sepay-webhook');

require __DIR__.'/settings.php';
