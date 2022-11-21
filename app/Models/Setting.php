<?php

namespace App\Models;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    const GOT = 'global_order_timer';
    const IOT = 'idle_order_time';

    protected $fillable = [
        'key',
        'value',
        'user_id',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
    ];
}
