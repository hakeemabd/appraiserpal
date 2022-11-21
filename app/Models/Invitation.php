<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\WorkerGroupRepository;

class Invitation extends Model
{
    use SoftDeletes;
    protected $table = 'invitations';

    protected $fillable = [
        'user_id',
        'order_id',
        'group_id',
        'code',
        'sent_at',
        'rejected_at',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'sent_at', 'accepted_at', 'rejected_at'];

    public function group()
    {
        return $this->belongsTo(WorkerGroup::class, 'group_id');
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getTurnAroundTime()
    {
        $workerGroupRepository = app(WorkerGroupRepository::class);
        $turnaround = $workerGroupRepository->getWorkerTurnaround($this->user_id, $this->group_id, $this->order_id);
        $turnaroundHuman = '';
        if ($turnaround < 60) {
            $turnaroundHuman = $turnaround.'min';
        } else {
            $hrs = intval($turnaround/60);
            $min = $turnaround-($hrs*60);
            if (strlen($min) < 2) {
                $min = '0'.$min;
            }
            $turnaroundHuman = $hrs.':'.$min.'hrs';
        }

        return $turnaroundHuman;
    }
}
