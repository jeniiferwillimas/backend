<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('reference')->nullable()->index()->after('merchant_request_id');
        });

        Schema::table('mpesa_stk_push_requests', function (Blueprint $table) {
            $table->string('reference')->nullable()->index()->after('checkout_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('reference');
        });

        Schema::table('mpesa_stk_push_requests', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }
};
