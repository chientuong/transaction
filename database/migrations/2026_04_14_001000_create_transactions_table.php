<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 100)->unique();
            $table->foreignId('prefix_id')->constrained('payment_prefixes')->onDelete('restrict');
            $table->decimal('amount', 18, 2);
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->onDelete('restrict');
            $table->string('transfer_content', 500);
            
            $table->string('sync_status', 50)->default('PENDING');
            $table->string('ops_status', 50)->default('UNREVIEWED');
            
            $table->timestamp('expires_at');
            $table->timestamp('expired_at')->nullable();
            
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            
            $table->text('ops_note')->nullable();
            
            $table->timestamps();
            
            $table->index('sync_status');
            $table->index('ops_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
