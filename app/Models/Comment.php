<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Attachment;
//use Hootlex\Moderation\Moderatable;

class Comment extends Model
{
    // use Moderatable;
    
    const PRIVATE_CHANNEL = 'private';
    const PUBLIC_CHANNEL = 'public';

    protected $visible = [
        'id',
        'parent_id',
        'user_id',
        'order_id',
        'role_id',
        'namespace',
        'content',
        'status',
        'approved_by',
        'moderated_at',
        'moderated_by',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'parent_id',
        'user_id',
        'order_id',
        'role_id',
        'namespace',
        'content',
        'status',
        'approved_by',
        'moderated_at',
        'moderated_by',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attachment()
    {
        return $this->hasMany(Attachment::class);
    }
}
