<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sepay_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('sepay_id')->nullable()->comment('ID giao dịch trên SePay');
            $table->string('gateway')->nullable()->comment('Brand name của ngân hàng');
            $table->timestamp('transaction_date')->nullable()->comment('Thời gian xảy ra giao dịch phía ngân hàng');
            $table->string('account_number')->nullable()->comment('Số tài khoản ngân hàng');
            $table->string('sub_account')->nullable()->comment('Tài khoản ngân hàng phụ');
            $table->decimal('amount_in', 15, 2)->default(0)->comment('Tiền vào');
            $table->decimal('amount_out', 15, 2)->default(0)->comment('Tiền ra');
            $table->decimal('accumulated', 15, 2)->default(0)->comment('Số dư tài khoản');
            $table->string('code')->nullable()->comment('Mã code thanh toán sepay tự nhận diện');
            $table->string('content')->nullable()->comment('Nội dung chuyển khoản');
            $table->string('reference_code')->nullable()->comment('Mã tham chiếu');
            $table->text('description')->nullable()->comment('Toàn bộ nội dung tin nhắn');
            $table->json('raw_data')->nullable()->comment('Dữ liệu thô từ webhook');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sepay_transactions');
    }
};
