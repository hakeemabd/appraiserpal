<?php

namespace App\Models;

use App\Repositories\UserRepository;
use App\Repositories\TransactionRepository;
use App\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\SettingRepository;

class Order extends Model
{
    use SoftDeletes;
    const ADJ_TYPE_NONE = 'none';
    const ADJ_TYPE_ADD_REGRESSION = 'add regression';
    const ADJ_TYPE_UPLOAD = 'own';

    const STATUS_NEW = 'new';
    const STATUS_CREATING = 'creating';
    const STATUS_REWORKING = 'reworking';
    const STATUS_DONE = 'done';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_CANCELED = 'canceled';
    const STATUS_SENT_BACK = 'sent back';

    const WORK_STATUS_INVITING = 'inviting';
    const WORK_STATUS_ASSIGNING = 'assigning';
    const WORK_STATUS_ON_HOLD = 'onhold';
    const WORK_STATUS_WORKING = 'working';
    const WORK_STATUS_FINISHED = 'finished';
    const WORK_STATUS_OVERDUE = 'overdue';

    const WINTOTAL_DISCOUNT = 5;
    const SINGLE_REPORT_PRICE = 90;

    protected $visible = [
        'id',
        'title',
        'software_id',
        'forms_type',
        'effective_date',
        'assignment_type',
        'occupancy_type',
        'financing',
        'property_rights',
        'specific_instructions',
        'adjustment_type',
        'property_rights',
        'user_id',
        'property_photo',
        'forms_type',
        'report_type_id',
        'completed',
        'standard_instructions',
        'order_transaction',
        'orderTimeLeft',
        'customerStatus',
        'reportType',
        'user',
        'price',
        'created_at',
        'is_completed',
        'sent_idle'
    ];

    protected $table = 'orders';

    protected $fillable = [
        'title',
        'software_id',
        'forms_type',
        'effective_date',
        'category_id',
        'assignment_type',
        'occupancy_type',
        'financing',
        'property_rights',
        'specific_instructions',
        'adjustment_type',
        'property_rights',
        'user_id',
        'property_photo',
        'forms_type',
        'report_type_id',
        'completed',
        'standard_instructions',
        'status',
        'is_completed',
        'sent_idle'
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'deadline'];

    protected $hidden = ['updated_at'];
//
    protected $appends = ['orderTimeLeft', 'customerStatus', 'order_transaction', 'price'];

    public static function formatStatus($params)
    {
        if ($params === null) {
            //get params from the current order
        }
        $statusTitles = [
            Order::STATUS_DONE => 'Finished',
            Order::STATUS_DELIVERED => 'Completed',
        ];
        $groupStatusTitles = [
            Order::WORK_STATUS_INVITING => 'Uninvited',
            Order::WORK_STATUS_ASSIGNING => 'Unassigned',
        ];
        $workingStatuses = [Order::STATUS_NEW, Order::STATUS_CREATING, Order::STATUS_REWORKING];
        if ($params['group_name'] && in_array($params['status'], $workingStatuses)) {
            if ($params['group_status'] == Order::WORK_STATUS_WORKING && $params['status'] == Order::STATUS_REWORKING) {
                return $params['group_name'] . ' (rework)';
            }
            $statusText = isset($groupStatusTitles[$params['group_status']]) ?
                $groupStatusTitles[$params['group_status']] :
                ucfirst($params['group_status']);
            return $params['group_name'] . ' (' . $statusText . ')';
        }
        return isset($statusTitles[$params['status']]) ? $statusTitles[$params['status']] : ucfirst($params['status']);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function firstImage()
    {
        return $this->hasMany(Attachment::class)->where('type', Attachment::TYPE_PHOTO)->take(1);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function reportType()
    {
        return $this->belongsTo(ReportType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getOrderPrice($inTrial, $isSubscribed, $hasFreeOrders)
    {
        $report_price = $this->reportType ? $this->reportType->current_price : 0;
        if ($inTrial && !$isSubscribed && !$hasFreeOrders) {
            $price = $report_price;
        } elseif ($isSubscribed || $hasFreeOrders) {
            $price = 0;
        } else {
            $price = $report_price;
        }
        return $price;
    }

    public function getPriceAttribute()
    {
        $userRepository = app(UserRepository::class);
        $TransactionRepository = app(TransactionRepository::class);
        $user = Sentinel::getUser();
        $subscriptionDetails = $userRepository->getStripeSubscriptionDetails($user->stripe_subscription, env('STRIPE_API_SECRET'));
        $subscriptionInfo = $TransactionRepository->getSubscriptionInfo($subscriptionDetails, $user, $userRepository);
//        $inTrial = $userRepository->inTrial($user);
//        $isSubscribed = $user->subscribed();
        $inTrial = $subscriptionInfo[0];
        $isSubscribed = $subscriptionInfo[1];
        $price = $this->getOrderPrice($inTrial, $isSubscribed, $user->hasFreeOrders());
        if ($this->hasTransaction()) {
            return $this->transaction->amount;
        }
        return $price; 
    }

    public function canEdit()
    {
        return $this->status == self::STATUS_NEW;
    }

    public function getOrderTransactionAttribute()
    {
        return $this->transaction;
    }

    public function canDownload()
    {
        return in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_ACCEPTED, self::STATUS_ARCHIVED]);
    }

    public function canComment()
    {
        return !in_array($this->status, [self::STATUS_ARCHIVED, self::STATUS_CANCELED]);
    }

    public function hasTransaction()
    {
        return isset($this->transaction->id) ? true : false;
    }

    public function canCancel()
    {
        return $this->status == self::STATUS_NEW;
    }

    public function canAccept()
    {
        return $this->status == self::STATUS_DELIVERED;
    }

    public function canReject()
    {
        return $this->status == self::STATUS_DELIVERED;
    }

    public function canLeaveFeedback()
    {
        return $this->status == self::STATUS_ACCEPTED && $this->feedbackRating == 0;
    }

    public function isPaid()
    {
        return $this->transaction && $this->transaction->isCompleted();
    }

    public function isFinished()
    {
        return in_array($this->status, [self::STATUS_CANCELED, self::STATUS_ACCEPTED, self::STATUS_ARCHIVED]);
    }

    public function getCustomerStatusAttribute()
    {
        if (in_array($this->status, [self::STATUS_CREATING, self::STATUS_REWORKING])) {
            return 'In progress';
        }
        if (in_array($this->status, [self::STATUS_DONE])) {
            if ($this->is_completed) {
                return 'Finished';
            } else {
                return 'In progress';
            }
        }
        if (in_array($this->status, [self::STATUS_SENT_BACK])) {
            return 'Sent back';
        }
        return ucfirst($this->status);
    }

    public function getOrderTimeLeftAttribute()
    {
        if (in_array($this->status, [self::STATUS_CANCELED, self::STATUS_ARCHIVED, self::STATUS_DELIVERED])) {
            return 'N/A';
        }
        /*if (!$this->deadline) {
            return '';
        }
        if ($this->deadline->isFuture()) {
            $mins = $this->deadline->diffInMinutes();
            $hours = floor($mins / 60);
            $mins = ($mins % 60);
            return ($hours < 9 ? '0' . $hours : $hours) . ':' . ($mins < 9 ? '0' . $mins : $mins);
        }*/

        if ($this->deadline !== null) {
            $settingRepository = app(SettingRepository::class);
            $model = $settingRepository->model();
            $setting = $settingRepository->getByKey($model::GOT);
            $got = $setting->value;
            $now = strtotime("now");
            $deadline = strtotime($this->deadline);
            $time_left = intval(round(abs($deadline - $now) / 60, 2));
            if ($now > $deadline) {
                return "00:00";
            }
            //$time_left = $got - $interval;
            $seconds = 60 - date_format($this->deadline, 's');
            /*$time_left_human = '';
            if ($time_left > 0) {
                if ($time_left < 60) {
                    $time_left_human = $time_left.'min';
                } else {
                    $hrs = intval($time_left/60);
                    $min = $time_left-($hrs*60);
                    if (strlen($min) < 2) {
                        $min = '0'.$min;
                    }
                    $time_left_human = $hrs.':'.$min.'hrs';
                }
            } else {
                $time_left_human = '0min';
            }
            return $time_left_human;*/
            //return '<div id="time_left'.$this->id.'">'.$time_left_human.'</div>';
            return "
            <div id='countdown" . $this->id . "'></div>
            <script type='text/javascript'>

                function startTimer" . $this->id . "(time" . $this->id . ", seconds" . $this->id . ") {
                    var element" . $this->id . " = document.getElementById( 'countdown" . $this->id . "' );
                    var timer" . $this->id . " = (time" . $this->id . "*60)+seconds" . $this->id . ";
                    setInterval(function () {

                        var hours" . $this->id . " = parseInt(timer" . $this->id . " / 3600);
                        var minutes" . $this->id . " = parseInt((timer" . $this->id . " % 3600)/60);
                        var seconds" . $this->id . " = parseInt(timer" . $this->id . " % 60);

                        minutes" . $this->id . " = minutes" . $this->id . " < 10 ? '0' + minutes" . $this->id . " : minutes" . $this->id . ";
                        seconds" . $this->id . " = seconds" . $this->id . " < 10 ? '0' + seconds" . $this->id . " : seconds" . $this->id . ";

                        if (timer" . $this->id . " > 0) {
                            element" . $this->id . ".innerHTML = hours" . $this->id . "+':'+minutes" . $this->id . "+':'+seconds" . $this->id . "+' hrs';
                        } else {
                            element" . $this->id . ".innerHTML = '00:00';
                        }

                        --timer" . $this->id . ";
                    }, 1000);
                }
                startTimer" . $this->id . "(" . $time_left . ", " . $seconds . ");
            </script>";
        } else {
            return 'N/A';
        }
    }

    public function isCompleted()
    {
        return is_bool($this->completed) ? $this->completed : $this->completed == '1';
    }

    public function getUserId()
    {
        return $this->user_id;
    }
}
