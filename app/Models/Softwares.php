<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Softwares extends Model
{
    protected $fillable = [
        'name',
        'location'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
