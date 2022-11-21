<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryLog extends Model
{
    const NEW_ORDER = 'new_order';
    const PAYMENT = 'payment';
    const INVITED = 'invited';
    const ACEPT_INVITED = 'acept_invited';
    const UPLOAD_FILE = 'worker_upload_file';
    const WORKER_FINISHED = 'worker_finished';
    const WORKER_MARK_COMPLETED = 'worker_mark_completed';
    const ADMIN_MARK_COMPLETED = 'admin_mark_completed';
    const SEND_BACK = 'send_back';
    const ADMIN_APPROVED_FILE = 'admin_approved_file';
    const ADMIN_DISAPPROVED_FILE = 'admin_disapproved_file';
    const CUSTOMER_ACEPT_ORDER = 'customer_acept_order';

    protected $visible = [
        'id',
        'order_id',
        'description',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'order_id',
        'description',
        'created_at',
        'updated_at'
    ];
}
