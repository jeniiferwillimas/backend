<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_disbursements', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            
            // Disbursement Details
            $table->decimal('amount', 12, 2);
            $table->string('phone_number')->index();
            $table->string('mpesa_receipt_number')->nullable()->unique();
            
            // Disbursement Status
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'reversed'
            ])->default('pending');
            
            // Response Data
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            
            // Timestamps
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'phone_number']);
            $table->index('mpesa_receipt_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_disbursements');
    }
};