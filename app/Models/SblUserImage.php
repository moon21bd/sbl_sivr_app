<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SblUserImage extends Model
{
    protected $table = 'sbl_user_images';

    protected $fillable = [
        'user_id',
        'name',
        'user_phone',
        'user_account',
        'filename',
        'path',
    ];
}
