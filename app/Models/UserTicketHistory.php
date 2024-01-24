<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTicketHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile_no',
        'purpose',
        'account_no',
        'status',
    ];
}
