<?php

namespace App\Models;

use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Payment extends Model
{
    const STATUS_NEW = 'new';
    const STATUS_PAID = 'paid';
    const STATUS_DELAYED = 'delayed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_OVERDUE = 'overdue';

    protected $visible = [
        'id',
        'amount',
        'order_id',
        'user_id',
        'status',
        'is_free'
    ];

    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'status',
        'paid_at',
        'delayed_at',
        'delayed_until',
        'canceled_at',
        'overdue_at',
        'payment_id',
        'is_free'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted()
    {
        return $this->status = self::STATUS_PAID;
    }
}
