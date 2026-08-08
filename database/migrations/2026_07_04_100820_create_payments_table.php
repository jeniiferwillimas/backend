<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            
            // Payment Details
            $table->decimal('amount', 12, 2);
            $table->string('payment_type')->default('processing_fee'); // processing_fee, installment, full
            $table->string('payment_method')->default('mpesa'); // mpesa, bank, cash
            
            // M-PESA Details
            $table->string('phone_number')->index();
            $table->string('checkout_request_id')->nullable()->index();
            $table->string('merchant_request_id')->nullable()->index();
            $table->string('mpesa_receipt_number')->nullable()->unique();
            $table->string('transaction_id')->nullable()->unique();
            
            // Till/Paybill Details
            $table->string('till_number')->nullable();
            $table->string('account_number')->nullable();
            
            // Payment Status
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'cancelled',
                'refunded'
            ])->default('pending');
            
            // Payment Response Data
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->json('callback_payload')->nullable();
            
            // Timestamps
            $table->timestamp('payment_date')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'payment_date']);
            $table->index('mpesa_receipt_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};