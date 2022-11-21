<?php

namespace App\Repositories;

use App\Components\AwsS3Policy;
use App\Exceptions\SaveException;
use App\Models\Attachment;
use App\Models\Label;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AttachmentRepository extends BaseRepository
{

    const PREFIX_LENGTH = 10;

    /**
     * @var AwsS3Policy
     */
    protected $awsS3Helper;

    public function getFiles($orderId, $all = false)
    {
        $responseFiles = [];
        $files = $this->getOrderFiles($orderId, $all);
        foreach ($files as $file) {
            $responseFiles[$file['type']][] = $file;
        }

        return $responseFiles;
    }

    public function getOrderFiles($orderId, $all = false)
    {
        $condition = $this->model
            ->leftJoin('attachment_order', 'attachment_order.attachment_id', '=', 'attachments.id')
            ->where('attachments.order_id', $orderId)
            ->whereNull('attachments.comment_id')
            ->orWhere(function ($query) use ($orderId) {
                $query->where('attachment_order.order_id', $orderId)
                    ->whereNull('attachments.order_id')
                    ->whereNull('attachments.comment_id');;
            });
        //the condition below limits attachments to uploaded only by the customer of the order.
        //It implies that none except customer uploads sample files and clone files or it is OK for the customer to
        // get access to them at any moment.
        if ($all === false) {
            $condition->join('orders', function ($join) use ($orderId) {
                $join->on('attachments.user_id', '=', 'orders.user_id')
                    ->where('orders.id', '=', $orderId);
            });
        }
        return $condition->get();
    }

    public function getFinalOrderFiles($orderId, $all = false)
    {
        $condition = $this->model
            ->leftJoin('attachment_order', 'attachment_order.attachment_id', '=', 'attachments.id')
            ->where('attachments.order_id', $orderId)
            ->where('attachments.final', true)
            ->orWhere(function ($query) use ($orderId) {
                $query->where('attachment_order.order_id', $orderId)
                    ->whereNull('attachments.order_id');
            });
        //the condition below limits attachments to uploaded only by the customer of the order.
        //It implies that none except customer uploads sample files and clone files or it is OK for the customer to
        // get access to them at any moment.
        if ($all === false) {
            $condition->join('orders', function ($join) use ($orderId) {
                $join->on('attachments.user_id', '=', 'orders.user_id')
                    ->where('orders.id', '=', $orderId);
            });
        }
        return $condition->get();
    }

    public function getOrderInitialFiles($orderId)
    {
        return DB::table('attachments')
            ->select(DB::raw("
                attachments.*
                "))
            ->leftJoin('attachment_order', 'attachment_order.attachment_id', '=', 'attachments.id')
            ->where('attachments.order_id', $orderId)
            ->orWhere(function ($query) use ($orderId) {
                $query->where('attachment_order.order_id', $orderId)
                    ->whereNull('attachments.order_id');
            })
            ->join('orders', function ($join) use ($orderId) {
                $join->on('attachments.user_id', '=', 'orders.user_id')
                    ->where('orders.id', '=', $orderId);
            });
    }

    public function getOrderUploadedFiles($orderId)
    {
        return DB::table('attachments')
            ->select(DB::raw("
                attachments.*,
                users.email"))
            ->leftJoin('attachment_order', 'attachment_order.attachment_id', '=', 'attachments.id')
            ->where('attachments.order_id', $orderId)
            ->orWhere(function ($query) use ($orderId) {
                $query->where('attachment_order.order_id', $orderId)
                    ->whereNull('attachments.order_id');
            })
            ->leftJoin('users', 'users.id', '=', 'attachments.user_id')
            ->join('orders', function ($join) use ($orderId) {
                $join->on('attachments.user_id', '!=', 'orders.user_id')
                    ->where('orders.id', '=', $orderId);
            });
    }

    public function model()
    {
        return 'App\Models\Attachment';
    }

    /**
     * Saves file, links it to the order and attaches label. If label is integer, it will put label text.
     * Label ID is also saved in label_id field
     *
     * @param      $orderId
     * @param      $fileType
     * @param      $fileConfig
     *
     * @throws SaveException
     */
    public function saveFile($orderId, $fileType, $fileConfig, $commentId = null)
    {
        $attachment = false;
        $fileConfigDefault = [
            'id' => null,
            'key' => null,
            'label' => null,
            'report_type_id' => null,
            'software_id' => null,
            'label_id' => null,
            'user_id' => null,
        ];
        $fileConfig = array_merge($fileConfigDefault, $fileConfig);

        $labelId = null;
        if ($fileConfig['label'] !== null && (int)$fileConfig['label'] == $fileConfig['label']) { //if this is label ID
            $labelModel = Label::find($fileConfig['label']);
            if ($labelModel) {
                $fileConfig['label'] = $labelModel->name;
                $fileConfig['label_id'] = $labelModel->id;
            }
        }
 
        if ($fileConfig['id']) {
            $attachment = $this->updateAttachment($fileType, $fileConfig);
        } else {
            if (isset($fileConfig['key']) && !empty($fileConfig['key']))
                $attachment = $this->insertAttachment($orderId, $fileType, $fileConfig, $commentId);
        }
        if ($attachment !== false) {
            $this->linkToOrder($attachment, $orderId);
        }
    }

    /**
     * Updates attachment label and order it is linked to. Links attachments of all types.
     *
     * @param $fileType
     * @param $fileConfig
     *
     * @return bool|mixed
     * @throws SaveException
     */
    protected function updateAttachment($fileType, $fileConfig)
    {
        $attachment = $this->find($fileConfig['id']);

        /**
         * todo problem with label id
         */
//        $attachment->label = $fileConfig['label'];
//        $attachment->label_id = $fileConfig['label_id'];
//        $attachment->save();

        if (!$this->linkableThroughCrossTable($fileType)) {

            return false;
        } else {
            if ($attachment->user_id != $fileConfig['user_id']) {
                throw new SaveException("User does not have file with ID={$fileConfig['id']}");
            }
            if ($attachment->type != $fileType) {
                throw new SaveException("Bad file type format");
            }

            return $attachment;
        }
    }

    protected function linkableThroughCrossTable($type)
    {
        return $type == Attachment::TYPE_CLONE || $type == Attachment::TYPE_SAMPLE;
    }

    /**
     * Inserts attachment and links to the order.
     *
     * @param $orderId
     * @param $fileType
     * @param $fileConfig
     *
     * @return bool|mixed
     * @throws SaveException
     */
    protected function insertAttachment($orderId, $fileType, $fileConfig, $commentId)
    {
        $linkable = $this->linkableThroughCrossTable($fileType);

        $extension = pathinfo($fileConfig['key'], PATHINFO_EXTENSION);
        if ($extension == 'pdf') {
            $format = Attachment::FORMAT_DOC;
        } elseif (in_array($extension, $this->getImageExtensions())) {
            $format = Attachment::FORMAT_IMAGE;
        } else {
            $format = Attachment::FORMAT_OTHER;
        }

        $attachmentData = [
            'type' => $fileType,
            'order_id' => ($linkable) ? null : $orderId,
            'format' => $format,
            's3key' => $fileConfig['key'],
            'label' => $this->makeLabel($fileType, $fileConfig),
            'user_id' => $fileConfig['user_id'],
            'label_id' => $fileConfig['label_id'],
            'software_id' => ($linkable) ? $fileConfig['software_id'] : null,
            'report_type_id' => ($linkable) ? $fileConfig['report_type_id'] : null,
            'comment_id' => $commentId,
        ];

        $attachment = $this->create($attachmentData);
        if (!isset($attachment->id)) {
            throw new SaveException("$fileType not saved");
        }
        if ($linkable) {
            return $attachment;
        }

        return false;
    }

    protected function getImageExtensions()
    {
        return ['jpeg', 'jpg', 'png', 'bmp', 'gif', 'svg'];
    }

    /**
     * Creates label depending on the type. For the comparable it serializes all fields except key and id into JSON
     * since they represent the address
     *
     * @param $fileType
     * @param $fileDetails
     *
     * @return string
     */
    protected function makeLabel($fileType, $fileDetails)
    {
        if ($fileDetails['label']) {
            return $fileDetails['label'];
        }
        unset($fileDetails['key']);
        unset($fileDetails['id']);
        unset($fileDetails['software_id']);
        unset($fileDetails['order_type_id']);
        unset($fileDetails['label']);
        unset($fileDetails['report_type_id']);
        unset($fileDetails['label_id']);
        unset($fileDetails['user_id']);
        if (isset($fileDetails['photo'])) {
            //@todo remove when Andrey remove this from client
            unset($fileDetails['photo']);
        }
        return ($fileDetails) ? json_encode($fileDetails) : null;
    }

    /**
     * Links attachment to the order.
     *
     * @param Attachment $attachment
     * @param int $orderId
     */
    protected function linkToOrder(Attachment $attachment, $orderId)
    {
        if (!$attachment->sharedAttachment()->where('order_id', $orderId)->exists()) {
            $attachment->sharedAttachment()->attach($orderId);
        }
    }

    /**
     * @param $attachment
     *
     * @return bool
     */
    public function destroy(Attachment $attachment)
    {
        //@todo delete from s3
        $this->getPolicyGenerator();
        $this->awsS3Helper->delete($attachment->s3key);

        $attachment->sharedAttachment()->detach();
        $attachment->delete();
    }

    public function availableFileTypes()
    {
        static $types;
        if (!$types) {
            $types = array_keys($this->validationRules());
        }
        return $types;
    }

    protected function validationRules()
    {
        return [
            Attachment::TYPE_DATA_MOBILE => [
                'extensions' => ['pdf'], //filter for ng-upload validator
                'acceptMime' => ['application/pdf'], //filter for file upload dialog
                'maxSize' => Attachment::SIZE_10MB,
                'folder' => 'docs/',
                'fileName' => Attachment::TYPE_DATA_MOBILE //name of the file input field to be submitted to the S3 and to the server
            ],
            Attachment::TYPE_DATA_MANUAL => [
                //list of files that can be uploaded in manular forms
                'extensions' => ['pdf', /*'doc', 'docx',*/
                    'zip', 'rar', '7z'],
                'acceptMime' => [
                    'application/pdf',
//                    'application/msword',
//                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/zip',
                    'application/x-rar-compressed',
                    'application/x-7z-compressed',
                ],
                'maxSize' => Attachment::SIZE_50MB,
                'folder' => 'docs/',
                'fileName' => Attachment::TYPE_DATA_MANUAL
            ],
            Attachment::TYPE_PHOTO => [
                'extensions' => $this->getImageExtensions(),
                'acceptMime' => ['image/*'],
                'maxSize' => Attachment::SIZE_5MB,
                'folder' => 'photos/source/',
                'fileName' => Attachment::TYPE_PHOTO
            ],
            Attachment::TYPE_COMPARABLE => [
                'extensions' => $this->getImageExtensions(),
                'acceptMime' => ['image/*'],
                'maxSize' => Attachment::SIZE_5MB,
                'folder' => 'photos/source/',
                'fileName' => Attachment::TYPE_COMPARABLE
            ],
            Attachment::TYPE_MLS => [
                'extensions' => ['pdf', 'xls', 'xlsx'],
                'acceptMime' => [
                    'application/pdf',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ],
                'maxSize' => Attachment::SIZE_5MB,
                'folder' => 'docs/',
                'fileName' => Attachment::TYPE_MLS
            ],
            Attachment::TYPE_CLONE => [
                'extensions' => [
//                    'zap', 'clk', 'rpt', 'aci'
                ], //@todo: ask Lovina about this
                'acceptMime' => [
//                    'application/octet-stream',
//                    'application/x-rpt',
//                    'text/xml'
                ],
                'maxSize' => Attachment::SIZE_10MB,
                'folder' => 'datafiles/',
                'fileName' => Attachment::TYPE_CLONE
            ],
            Attachment::TYPE_SAMPLE => [
                'extensions' => ['pdf'], //@todo: ask Lovina about this
                'acceptMime' => ['application/pdf'], //no filter should be set in directive
                'maxSize' => Attachment::SIZE_10MB,
                'folder' => 'datafiles/',
                'fileName' => Attachment::TYPE_SAMPLE
            ],
            Attachment::TYPE_SKETCH => [
                'extensions' => ['pdf'/*, 'zip', 'rar', 'jpeg', 'jpg', 'png', 'gif', 'svg'*/],
                'acceptMime' => [
                    'application/pdf',
//                    'application/zip',
//                    'application/x-rar-compressed',
//                    'image/*'
                ],
                'maxSize' => Attachment::SIZE_10MB,
                'folder' => 'datafiles/',
                'fileName' => Attachment::TYPE_SKETCH
            ],
            Attachment::TYPE_MC1004 => [
                'extensions' => ['pdf', 'xls', 'xlsx'],
                'acceptMime' => [
                    'application/pdf',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ],
                'maxSize' => Attachment::SIZE_5MB,
                'folder' => 'docs/',
                'fileName' => Attachment::TYPE_MC1004
            ],
            Attachment::TYPE_ADJ_SHEETS => [
                'extensions' => ['pdf', 'xls', 'xlsx'],
                'acceptMime' => [
                    'application/pdf',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ],
                'maxSize' => Attachment::SIZE_5MB,
                'folder' => 'docs/',
                'fileName' => Attachment::TYPE_ADJ_SHEETS
            ],
            Attachment::TYPE_CONTRACT_INFO => [
                'extensions' => ['pdf'/*, 'doc', 'docx', 'xls', 'xlsx'*/],
                'acceptMime' => [
                    'application/pdf',
//                    'application/msword',
//                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
//                    'application/vnd.ms-excel',
//                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ],
                'maxSize' => Attachment::SIZE_5MB,
                'folder' => 'docs/',
                'fileName' => Attachment::TYPE_CONTRACT_INFO
            ],
            Attachment::TYPE_SUBJECT_INFO => [
                'extensions' => ['pdf'/*, 'doc', 'docx'*/],
                'acceptMime' => [
                    'application/pdf',
//                    'application/msword',
//                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ],
                'maxSize' => Attachment::SIZE_5MB,
                'folder' => 'docs/',
                'fileName' => Attachment::TYPE_SUBJECT_INFO
            ],
            Attachment::TYPE_COMPARABLE_INFO => [
                'extensions' => ['pdf'/*, 'doc', 'docx'*/],
                'acceptMime' => [
                    'application/pdf',
//                    'application/msword',
//                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ],
                'maxSize' => Attachment::SIZE_5MB,
                'folder' => 'docs/',
                'fileName' => Attachment::TYPE_COMPARABLE_INFO
            ],
            Attachment::TYPE_MISCELLANEOUS => [
                'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'jpeg', 'jpg', 'png', 'gif', 'svg'],
                'acceptMime' => [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/zip',
                    'application/x-rar-compressed',
                    'image/*'
                ],
                'maxSize' => Attachment::SIZE_10MB,
                'folder' => 'datafiles/',
                'fileName' => Attachment::TYPE_MISCELLANEOUS
            ],
            Attachment::TYPE_ANY => [
                'extensions' => [
                ],
                'acceptMime' => [
                ],
                'maxSize' => Attachment::SIZE_10MB,
                'folder' => 'docs/',
                'fileName' => Attachment::TYPE_ANY,
                'success_action_status' => "201"
            ]
        ];
    }

    /**
     * Returns a list of file types available for upload with the validation details, S3 policy and signature to upload
     * directly to the S3 bucket.
     *
     * This config assumes that frontend uses ng-file-upload @see https://github.com/danialfarid/ng-file-upload
     * But any other frontend package would do just fine.
     *
     * Config has the following structure:
     *  - [uploadUrl]    string  Full URL for uploading, including the bucket name
     *  - [accessKey]    string  AWS Access ID, needed for uploading files to Amazon S3
     *  - [filePrefix]   string  Prefix that should be prepended to every file name uploaded. This guarantees
     *                           uniqueness of the names
     *  - [uploadConfig] array   associative array where key is the file type and value has the following parameters:
     *    - [extensions] array   List of allowed extensions. Should go to the ngf-pattern property.
     *    - [acceptMime] array   List of allowed mime types. Should go to ngf-accept property which follows this specs:
     *                           http://www.w3schools.com/tags/att_input_accept.asp
     *    - [maxSize]    int     Maximum size of the file
     *    - [folder]     string  Name of the folder where file shoild be uploaded. This is S3 key prefix.
     *                           $key=$folder.$filePrefix.$selectedFileName
     *    - [fileName]   string  Name of the file field. S3 key should be sent to the server in the field with this name
     *    - [policy]     string  Base64-encoded JSON policy for the upload for a given file type. It is generated based
     *                           on the rules listed above
     *    - [signature]  string  Base64-encoded signature for the policy
     *
     * @return array
     */
    public function getUploadConfig()
    {
        $uploadUrl = 'https://' . env('S3_BUCKET') . '.s3-' . env('S3_REGION') . '.amazonaws.com/';
        $accessKey = env('S3_WRITE_USER_KEY');
        $uploadConfig = [];
        $filePrefix = str_random(self::PREFIX_LENGTH) . '_';
        foreach ($this->validationRules() as $type => $rule) {
            $ruleS3Schema = $this->getS3FilesSchema()[$type];
            $uploadConfig[$type] = array_merge($rule, $this->getPolicyGenerator()->getUploadPolicyWithSignature($ruleS3Schema));
        }

        return compact('uploadUrl', 'accessKey', 'filePrefix', 'uploadConfig');
    }

    protected function getS3FilesSchema()
    {
        static $s3Schema;
        if (!$s3Schema) { //simple caching
            foreach ($this->validationRules() as $type => $rule) {
                $s3Schema[$type] = $this->convertRule($rule);
            }
        }
        return $s3Schema;
    }

    protected function convertRule($rule)
    {
        $s3Rule = [];
        if (sizeof($rule['acceptMime']) == 1) {
            $s3Rule['Content-Type'] = $rule['acceptMime'][0];
            //chop the last '*' if present
            if ($s3Rule['Content-Type'][strlen($s3Rule['Content-Type']) - 1] == '*') {
                $s3Rule['Content-Type'] = substr($s3Rule['Content-Type'], 0, strlen($s3Rule['Content-Type']) - 1);
            }
        } else {
            $s3Rule['Content-Type'] = '';
        }
        if (isset($rule['folder'])) {
            $s3Rule['key'] = $rule['folder'];
        }
        $s3Rule['size'] = [0, $rule['maxSize']];
        if (isset($rule['success_action_status'])) {
            $s3Rule['success_action_status'] = $rule['success_action_status'];
        }
        return $s3Rule;
    }

    protected function getPolicyGenerator()
    {
        if (!$this->awsS3Helper) {
            $this->awsS3Helper = new AwsS3Policy();
        }
        return $this->awsS3Helper;
    }

    public function mark(Attachment $attachment, $isFinal)
    {
        $attachment->final = $isFinal;
        $attachment->save();
    }

    public function approve(Attachment $attachment, $isApproved)
    {
        $attachment->approved = $isApproved;
        $attachment->save();
    }
}
