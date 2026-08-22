<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'loan_application_id',
        'amount',
        'payment_type',
        'payment_method',
        'phone_number',
        'checkout_request_id',
        'merchant_request_id',
        'reference',
        'mpesa_receipt_number',
        'transaction_id',
        'till_number',
        'account_number',
        'status',
        'request_payload',
        'response_payload',
        'callback_payload',
        'payment_date',
        'confirmed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'callback_payload' => 'array',
        'payment_date' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }

    public function stkPush()
    {
        return $this->hasOne(MpesaStkPushRequest::class);
    }
}