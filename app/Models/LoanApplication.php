<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanApplication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'full_name',
        'phone_number',
        'national_id',
        'amount',
        'interest_rate',
        'term_days',
        'processing_fee',
        'total_repayment',
        'status',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'total_repayment' => 'decimal:2',
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function disbursement()
    {
        return $this->hasOne(LoanDisbursement::class);
    }

    public function repayments()
    {
        return $this->hasMany(LoanRepayment::class);
    }

    public function activities()
    {
        return $this->hasMany(LoanActivity::class);
    }
}