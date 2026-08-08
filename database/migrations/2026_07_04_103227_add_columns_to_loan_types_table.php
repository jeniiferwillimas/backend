<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('min_amount', 12, 2);
            $table->decimal('max_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->decimal('processing_fee', 12, 2)->default(0);
            $table->integer('min_tenure_months')->default(1);
            $table->integer('max_tenure_months')->default(12);
            $table->boolean('requires_guarantor')->default(false);
            $table->boolean('requires_collateral')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'slug',
                'description',
                'min_amount',
                'max_amount',
                'interest_rate',
                'processing_fee',
                'min_tenure_months',
                'max_tenure_months',
                'requires_guarantor',
                'requires_collateral',
                'is_active',
                'sort_order'
            ]);
        });
    }
};