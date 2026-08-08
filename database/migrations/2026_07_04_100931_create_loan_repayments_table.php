<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            
            // Repayment Details
            $table->integer('installment_number');
            $table->decimal('amount', 12, 2);
            $table->decimal('principal', 12, 2);
            $table->decimal('interest', 12, 2);
            $table->date('due_date');
            
            // Repayment Status
            $table->enum('status', [
                'pending',
                'paid',
                'overdue',
                'partial',
                'defaulted'
            ])->default('pending');
            
            // Late Payment
            $table->decimal('late_fee', 12, 2)->default(0);
            $table->integer('days_overdue')->default(0);
            $table->timestamp('paid_at')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['loan_application_id', 'installment_number']);
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
    }
};