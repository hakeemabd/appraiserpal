<?php

namespace App\Models;

use App\Components\S3StorableTrait;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use S3StorableTrait;

    const TYPE_DATA_MOBILE = 'data_file_mobile'; //1 step
    const TYPE_DATA_MANUAL = 'data_file_manual'; //1 step
    const TYPE_CONTRACT_INFO = 'contract'; //1 step
    const TYPE_SUBJECT_INFO = 'subject'; //1 step
    const TYPE_SKETCH = 'sketch'; //1 step
    const TYPE_MC1004 = 'mc_1004'; //1 step
    const TYPE_MLS = 'mls'; // 1 step
    const TYPE_COMPARABLE_INFO = 'comparable_info'; //1 step
    const TYPE_CLONE = 'clone'; //1 step
    const TYPE_MISCELLANEOUS = 'miscellaneous'; //1 step
    const TYPE_COMPARABLE = 'comparables'; //2 step
    const TYPE_SAMPLE = 'sample';// 3 step
    const TYPE_ADJ_SHEETS = 'adj_sheets'; //4 step
    const TYPE_PHOTO = 'photo'; //5 step
    const TYPE_ANY = 'any'; //worker and admin site

    const FORMAT_DOC = 'document';
    const FORMAT_IMAGE = 'image';
    const FORMAT_OTHER = 'other';

    const IMG_SMALL = 'small';
    const IMG_MEDIUM = 'medium';
    const IMG_SOURCE = 'source';

    const DEFAULT_IMAGE = '/images/default-no-image.png';

    const SIZE_5MB = 5242880;
    const SIZE_10MB = 52428800;//10485760
    const SIZE_20MB = 20971520;
    const SIZE_50MB = 52428800;

    const URL_LIFETIME = 20;//20 mins

    protected $table = 'attachments';
 
    protected $visible = [
        'id',
        'label',
        'type',
        'software_id',
        'report_type_id',
        'path',
        'name',
        'label_id',
        'final',
        'approved',
        'comment_id'
    ];

    protected $fillable = [
        'format',
        'type',
        's3key',
        'label',
        'order_id',
        'user_id',
        'label_id',
        'software_id',
        'report_type_id',
        'final',
        'approved',
        'comment_id'
    ];

    protected $appends = [
        'path',
        'name',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function getPathAttribute()
    {
        return $this->getS3Url();
    }

    public function getNameAttribute()
    {
        return substr($this->s3key, strpos($this->s3key, '_') + 1, strlen($this->s3key));
    }

    public function labels()
    {
        return $this->belongsTo('App\Models\Label');
    }

    public function sharedAttachment()
    {
        return $this->belongsToMany('App\Models\Order');
    }

    public function formatLabel()
    {
        if ($this->type != self::TYPE_COMPARABLE) {

            return isset($this->label) ? $this->label : $this->name;
        }

        $components = [];
        $label = json_decode($this->label, true);
        foreach (['address1', 'address2', 'city', 'state', 'zip'] as $field) {
            if (isset($label[$field])) {
                $components[] = $label[$field];
            }
        }
        return implode(', ', $components);
    }

    public function getImageUrl($size)
    {
        return $this->getS3Url(self::URL_LIFETIME, str_replace('/source/', "/$size/", $this->s3key));
    }

    protected function getS3Key()
    {
        return $this->s3key;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getlabel()
    {
        return $this->label;
    }
}
