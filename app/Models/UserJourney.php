<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserJourney extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'user_phone_no', 'user_account_no', 'page', 'action', 'data', 'browser', 'ip_address',
    ];
}
