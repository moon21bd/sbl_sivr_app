<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpHistory extends Model
{
    protected $table = 'otp_history';
    protected $fillable = [
        'phone_number',
        'otp_sent_count',
        'last_sent_at',
        'otp_sent_success',
        'response_status_code',
        'response_received_at',
        'response_data',
    ];
}
