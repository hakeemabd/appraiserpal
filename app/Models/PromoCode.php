<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromoCode extends Model
{
    use SoftDeletes;
    const CODE_LENGTH = 8;
    protected $visible = [
        'id',
        'code',
        'percent',
        'count'
    ];

    protected $fillable = [
        'id',
        'code',
        'percent',
        'count',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function canApplyCode()
    {
        return $this->count > 0;
    }
}
