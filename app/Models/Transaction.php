<?php

namespace App\Models;

use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
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
        'status',
        'promo_code_id',
        'is_free'
    ];

    protected $fillable = [
        'order_id',
        'amount',
        'status',
        'paid_at',
        'delayed_at',
        'delayed_until',
        'canceled_at',
        'overdue_at',
        'payment_id',
        'promo_code_id',
        'is_free',
        'paid_by'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function hasPromoCode()
    {
        return is_null($this->promo_code_id);
    }

    public function isCompleted()
    {
        return $this->status = self::STATUS_PAID;
    }

    public function charge()
    {
        $userRepository = app(UserRepository::class);
        if ($userRepository->charge($this->order->user, $this->amount)) {
            return self::STATUS_PAID;
        } else {
            return self::STATUS_OVERDUE;
        }
    }
}
