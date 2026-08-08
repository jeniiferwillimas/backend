<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    protected $fillable = [
        'loan_application_id',
        'payment_id',
        'installment_number',
        'amount',
        'principal',
        'interest',
        'due_date',
        'status',
        'late_fee',
        'days_overdue',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'principal' => 'decimal:2',
        'interest' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}