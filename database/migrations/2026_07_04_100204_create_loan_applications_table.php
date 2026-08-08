<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            
            // Personal Information
            $table->string('full_name');
            $table->string('phone_number')->index();
            $table->string('national_id')->unique()->index();
            
            // Loan Type
            $table->foreignId('loan_type_id')->nullable()->constrained()->onDelete('set null');
            
            // Loan Details
            $table->decimal('amount', 12, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('term_days');
            $table->decimal('processing_fee', 12, 2)->default(0);
            $table->decimal('total_repayment', 12, 2);
            
            // Status
            $table->enum('status', [
                'pending',
                'approved',
                'processing_payment',
                'payment_pending',
                'disbursed',
                'completed',
                'rejected',
                'cancelled'
            ])->default('pending');
            
            // User tracking
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'phone_number']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};