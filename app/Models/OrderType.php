<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderType extends Model
{
    protected $fillable = ['id', 'name'];

    protected $hidden = ['created_at', 'updated_at'];
}
