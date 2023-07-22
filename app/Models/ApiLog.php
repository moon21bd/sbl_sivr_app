<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
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
