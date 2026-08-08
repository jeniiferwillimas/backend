<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanDisbursement extends Model
{
    protected $fillable = [
        'loan_application_id',
        'payment_id',
        'amount',
        'phone_number',
        'mpesa_receipt_number',
        'status',
        'request_payload',
        'response_payload',
        'disbursed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'disbursed_at' => 'datetime',
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