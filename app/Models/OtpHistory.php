<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpHistory extends Model
{
    protected $table = 'otp_history';

    protected $fillable = [
        'mobile_no',
        'otp',
        'purpose',
        'otp_used',
        'api_status_code',
        'api_response',
        'otp_sent_at',
    ];

    protected $casts = [
        'otp_sent_at' => 'datetime', // Cast 'otp_sent_at' as a datetime field
    ];

}
