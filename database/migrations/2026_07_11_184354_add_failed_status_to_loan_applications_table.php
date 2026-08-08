<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Postgres has no native ENUM type here (status is varchar + CHECK), and
        // combining a TYPE change with an inline CHECK in one ALTER COLUMN isn't
        // valid Postgres syntax the way the schema builder emits it for ->change().
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE loan_applications DROP CONSTRAINT IF EXISTS loan_applications_status_check');
            DB::statement("ALTER TABLE loan_applications ADD CONSTRAINT loan_applications_status_check CHECK (status IN ('pending','approved','processing_payment','payment_pending','disbursed','completed','rejected','cancelled','failed'))");
            return;
        }

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'approved',
                'processing_payment',
                'payment_pending',
                'disbursed',
                'completed',
                'rejected',
                'cancelled',
                'failed',
            ])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE loan_applications DROP CONSTRAINT IF EXISTS loan_applications_status_check');
            DB::statement("ALTER TABLE loan_applications ADD CONSTRAINT loan_applications_status_check CHECK (status IN ('pending','approved','processing_payment','payment_pending','disbursed','completed','rejected','cancelled'))");
            return;
        }

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'approved',
                'processing_payment',
                'payment_pending',
                'disbursed',
                'completed',
                'rejected',
                'cancelled',
            ])->default('pending')->change();
        });
    }
};
