<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class Worklog extends Model
{
    use SerializesModels;
    
    protected $table = 'worklogs';

    protected $hidden = ['created_at', 'updated_at'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'deadline', 'started_at', 'finished_at'];
}
