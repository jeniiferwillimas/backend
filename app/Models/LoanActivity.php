<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanActivity extends Model
{
    protected $fillable = [
        'loan_application_id',
        'causer_id',
        'causer_type',
        'action',
        'status',
        'description',
        'metadata',
        'previous_state',
        'current_state',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'metadata' => 'array',
        'previous_state' => 'array',
        'current_state' => 'array',
    ];

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }

    public function causer()
    {
        return $this->morphTo();
    }
}