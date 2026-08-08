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
        Schema::table('mpesa_stk_push_requests', function (Blueprint $table) {
            $table->string('result_code')->nullable()->after('status');
            $table->string('result_desc')->nullable()->after('result_code');
            $table->string('mpesa_receipt_number')->nullable()->after('result_desc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpesa_stk_push_requests', function (Blueprint $table) {
            $table->dropColumn(['result_code', 'result_desc', 'mpesa_receipt_number']);
        });
    }
};
