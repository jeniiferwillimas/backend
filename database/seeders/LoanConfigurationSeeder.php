<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('loan_configurations')->insert([
            [
                'key' => 'mpesa_till_number',
                'value' => '123456',
                'group' => 'mpesa',
                'description' => 'M-PESA Till Number for payments',
                'is_encrypted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mpesa_consumer_key',
                'value' => env('MPESA_CONSUMER_KEY', 'test_key'),
                'group' => 'mpesa',
                'description' => 'M-PESA API Consumer Key',
                'is_encrypted' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mpesa_consumer_secret',
                'value' => env('MPESA_CONSUMER_SECRET', 'test_secret'),
                'group' => 'mpesa',
                'description' => 'M-PESA API Consumer Secret',
                'is_encrypted' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_interest_rate',
                'value' => '8.0',
                'group' => 'loan',
                'description' => 'Default interest rate for loans',
                'is_encrypted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_term_days',
                'value' => '180',
                'group' => 'loan',
                'description' => 'Default loan term in days (6 months)',
                'is_encrypted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'processing_fee_percentage',
                'value' => '4.59',
                'group' => 'loan',
                'description' => 'Processing fee percentage of loan amount',
                'is_encrypted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}