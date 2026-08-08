<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanConfiguration extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'description',
        'is_encrypted'
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    public static function getValue($key, $default = null)
    {
        $config = self::where('key', $key)->first();
        return $config ? $config->value : $default;
    }
}