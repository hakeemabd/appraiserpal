<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $table = 'bids';

    protected $fillable = [
        'user_id',
        'order_id',
        'group_id',
        'bid_amount',
     
    ];
}
