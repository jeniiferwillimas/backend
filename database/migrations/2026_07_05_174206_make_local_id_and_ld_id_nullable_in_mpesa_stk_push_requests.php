<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mpesa_stk_push_requests', function (Blueprint $table) {
            $table->string('local_id')->nullable()->change();
            $table->string('ld_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('mpesa_stk_push_requests', function (Blueprint $table) {
            $table->string('local_id')->nullable(false)->change();
            $table->string('ld_id')->nullable(false)->change();
        });
    }
};