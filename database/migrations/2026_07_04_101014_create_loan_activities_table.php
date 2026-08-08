<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_activities', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            $table->nullableMorphs('causer'); // User or System
            
            // Activity Details
            $table->string('action');
            $table->string('status');
            $table->text('description')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->json('previous_state')->nullable();
            $table->json('current_state')->nullable();
            
            // IP Tracking
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['action', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_activities');
    }
};