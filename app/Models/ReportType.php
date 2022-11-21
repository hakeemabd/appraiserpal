<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportType extends Model
{
    use SoftDeletes;

    const DEFAULT_REPORT_ID = 1;

    protected $visible = ['id', 'name', 'current_price', 'old_price'];

    protected $fillable = ['id', 'name', 'current_price', 'old_price'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $dates = ['deleted_at'];
}
