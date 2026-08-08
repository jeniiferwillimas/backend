<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            // Add application_reference if it doesn't exist
            if (!Schema::hasColumn('loan_applications', 'application_reference')) {
                $table->string('application_reference')->unique()->nullable();
            }
            
            // Add payment_confirmed_at if it doesn't exist
            if (!Schema::hasColumn('loan_applications', 'payment_confirmed_at')) {
                $table->timestamp('payment_confirmed_at')->nullable();
            }
            
            // Add disbursed_at if it doesn't exist
            if (!Schema::hasColumn('loan_applications', 'disbursed_at')) {
                $table->timestamp('disbursed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn('application_reference');
            $table->dropColumn('payment_confirmed_at');
            $table->dropColumn('disbursed_at');
        });
    }
};