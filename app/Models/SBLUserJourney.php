<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SBLUserJourney extends Model
{

    protected $table = 'sbl_user_journeys';

    protected $fillable = [
        'user_id', 'user_phone_no', 'user_account_no', 'page', 'action', 'data', 'browser', 'ip_address',
    ];
}
