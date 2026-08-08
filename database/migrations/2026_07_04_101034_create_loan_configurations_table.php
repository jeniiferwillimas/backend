<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_configurations', function (Blueprint $table) {
            $table->id();
            
            $table->string('key')->unique()->index();
            $table->text('value');
            $table->string('group')->default('general');
            $table->text('description')->nullable();
            $table->boolean('is_encrypted')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_configurations');
    }
};