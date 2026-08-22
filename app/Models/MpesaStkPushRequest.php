<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaStkPushRequest extends Model
{
    protected $fillable = [
        'payment_id',
        'loan_application_id',
        'merchant_request_id',
        'checkout_request_id',
        'reference',
        'local_id',
        'ld_id',
        'phone_number',
        'amount',
        'request_data',
        'status',
        'response_data',
        'callback_data',
        'webhook_url',
        'poll_attempts',
        'last_poll_at',
        'sent_at',
        'completed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_data' => 'array',
        'response_data' => 'array',
        'callback_data' => 'array',
        'last_poll_at' => 'datetime',
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }
}