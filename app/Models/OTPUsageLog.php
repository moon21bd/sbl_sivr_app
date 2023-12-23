<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTPUsageLog extends Model
{
    protected $table = 'otp_usage_logs';

    protected $fillable = [
        'mobile_no',
        'otp',
        'purpose',
        'is_valid',
        'api_status_code',
        'api_response',
        // 'otp_updated_at', // This is automatically handled by Laravel due to useCurrent() in the migration
        // 'created_at', // These timestamps are automatically handled by Laravel
        // 'updated_at',
    ];

    protected $casts = [
        'otp_updated_at' => 'datetime', // Cast 'otp_updated_at' as a datetime field
    ];
}
