<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpesa_stk_push_requests', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            
            // M-PESA Request Details
            $table->string('merchant_request_id')->unique()->index();
            $table->string('checkout_request_id')->unique()->index();
            $table->string('local_id')->index();
            $table->string('ld_id')->nullable();
            
            // Customer Details
            $table->string('phone_number')->index();
            $table->decimal('amount', 12, 2);
            
            // Request Data
            $table->json('request_data')->nullable();
            
            // STK Push Status
            $table->enum('status', [
                'pending',
                'sent',
                'processing',
                'completed',
                'failed',
                'timeout',
                'cancelled'
            ])->default('pending');
            
            // Response Data
            $table->json('response_data')->nullable();
            $table->json('callback_data')->nullable();
            
            // Webhook Details
            $table->string('webhook_url')->nullable();
            $table->integer('poll_attempts')->default(0);
            $table->timestamp('last_poll_at')->nullable();
            
            // Timestamps
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'checkout_request_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_stk_push_requests');
    }
};