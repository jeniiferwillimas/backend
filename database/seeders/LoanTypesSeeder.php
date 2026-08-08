<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanTypesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('loan_types')->insert([
            [
                'name' => 'Personal Loan',
                'slug' => 'personal',
                'description' => 'Flexible loans for personal needs and emergencies',
                'min_amount' => 1000,
                'max_amount' => 50000,
                'interest_rate' => 8.5,
                'processing_fee' => 500,
                'min_tenure_months' => 1,
                'max_tenure_months' => 6,
                'requires_guarantor' => false,
                'requires_collateral' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Business Loan',
                'slug' => 'business',
                'description' => 'Grow your business with working capital',
                'min_amount' => 5000,
                'max_amount' => 200000,
                'interest_rate' => 12.0,
                'processing_fee' => 1000,
                'min_tenure_months' => 3,
                'max_tenure_months' => 12,
                'requires_guarantor' => true,
                'requires_collateral' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Education Loan',
                'slug' => 'education',
                'description' => 'Invest in your education and future',
                'min_amount' => 2000,
                'max_amount' => 100000,
                'interest_rate' => 7.5,
                'processing_fee' => 500,
                'min_tenure_months' => 6,
                'max_tenure_months' => 24,
                'requires_guarantor' => true,
                'requires_collateral' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emergency Loan',
                'slug' => 'emergency',
                'description' => 'Quick funds for unexpected expenses',
                'min_amount' => 500,
                'max_amount' => 30000,
                'interest_rate' => 10.0,
                'processing_fee' => 300,
                'min_tenure_months' => 1,
                'max_tenure_months' => 3,
                'requires_guarantor' => false,
                'requires_collateral' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Home Improvement',
                'slug' => 'home_improvement',
                'description' => 'Renovate and upgrade your home',
                'min_amount' => 10000,
                'max_amount' => 150000,
                'interest_rate' => 9.0,
                'processing_fee' => 1000,
                'min_tenure_months' => 6,
                'max_tenure_months' => 18,
                'requires_guarantor' => false,
                'requires_collateral' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}