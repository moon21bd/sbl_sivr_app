<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_info',
        'user_phone',
        'ip',
        'url',
        'status_code',
        'request',
        'response',
        'exception_type',
        'server_info',
        'response_time',
    ];
}
