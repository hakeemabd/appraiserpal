<?php

namespace App;

use App\Models\Order;
use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Contracts\Billable as BillableContract;

class User extends EloquentUser implements BillableContract
{
    use Billable, SoftDeletes;
    const FREE_ORDERS = 1;
    const TOTAL_ORDERS = 20;
    const SUBSCRIPTION_PLAN_ID = 'price_1H8y1lDXFtXijeHj4xk3ZO3k'; 
    const TRIAL_PERIOD = 7;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $dates = ['trial_ends_at', 'subscription_ends_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'last_name',
        'first_name',
        'permissions',

        'mobile_phone',
        'work_phone',
        'city',
        'standard_instructions',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip',
        'license_number',
        'auto_comments',
        'auto_invite',
        'auto_delivery',
        'available',
        'delayed_payment',
        'paypal_email',
        'bank_name',
        'account_number',
        'routing_number',
        'confirmed',
        'notes',
        'email_notification',
        'sms_notification',
        'free_order_count',
        'last_four',
        'card_type',

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'permissions', 'stripe_id'];

    protected $appends = ['fullName'];

    public function getProfile()
    {
        return $this->attributesToArray();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function hasDelayedPayment()
    {
        return ($this->delayed_payment == '0') ? false : $this->delayed_payment;
    }

    public function hasStripeToken()
    {
        return ($this->stripe_active == '1') ? true : false;
    }

    public function mayDelayPayment()
    {
        return (int)$this->delayed_payment > 0;
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\WorkerGroup', 'worker_groups', 'user_id', 'group_id')
            ->withPivot('fee', 'second_fee', 'first_turnaround', 'next_turnaround');
    }

    public function getFullNameAttribute()
    {
        return self::formatFullName($this->email, $this->first_name, $this->last_name);
    }

    public static function formatFullName($email, $fname, $lname)
    {
        if ($fname || $lname) {
            return join(' ', [$fname, $lname]);
        }
        return $email;
    }

    public function hasFreeOrders()
    {
        return ((int)$this->free_order_count > 0);
    }

    public function getId()
    {
        return $this->id;
    }
}
