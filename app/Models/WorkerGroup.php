<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkerGroup extends Model
{
    use SoftDeletes;

    protected $table = 'groups';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['name', 'sort'];

    public function workers()
    {
        return $this->belongsToMany('App\User', 'worker_groups', 'group_id', 'user_id')
            ->withPivot('fee', 'second_fee', 'first_turnaround', 'next_turnaround');
    }
}
