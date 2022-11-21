<?php

namespace App\Models;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Label extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'user_id'
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = ['canDelete'];

    public function getCanDeleteAttribute()
    {
        $user = Sentinel::check();
        return $this->user_id != null && (($this->user_id == $user->id) || $user->inRole('administrator') || $user->inRole('sub-admin'));
    }

    public function attachment()
    {
        return $this->hasMany('App\Models\Attachment');
    }

}
