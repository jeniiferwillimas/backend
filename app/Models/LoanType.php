<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    protected $fillable = [
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
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'requires_guarantor' => 'boolean',
        'requires_collateral' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function applications()
    {
        return $this->hasMany(LoanApplication::class);
    }
}