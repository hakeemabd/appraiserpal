<?php
namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Order;
use App\Repositories\AttachmentRepository;
use App\Repositories\HistoryLogRepository;
use App\Repositories\CommentRepository;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use App\Models\HistoryLog;
use App\Repositories\OrderRepository;
use App\Events\ApprovedComment;
use App\Events\ReturnOrderComment;

class AttachmentController extends Controller
{
    private $attachmentRepository;

    public function __construct(AttachmentRepository $attachmentRepository, HistoryLogRepository $historyLogRepository, CommentRepository $commentRepository, OrderRepository $orderRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
        $this->historyLogRepository = $historyLogRepository;
        $this->commentRepository = $commentRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Attachment $attachment
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Attachment $attachment)
    {
            $this->attachmentRepository->destroy($attachment);

            return response()->json([
                'success' => true,
                'message' => 'File successfully deleted',
            ], SymfonyResponse::HTTP_OK);
    }

    public function getUserFiles($type)
    {
        try {
            $resultData = [];
            $userFiles = $this->attachmentRepository->findWhere([
                'type' => $type,
                'user_id' => Sentinel::check()->id,
            ]);

            foreach ($userFiles as $userFile) {
                $resultData[] = [
                    'id' => (int)$userFile['id'],
                    'label' => $userFile['label'],
                    'path' => $userFile->getS3Url(),
                    'software_id' => $userFile['software_id'],
                    'report_type_id' => $userFile['report_type_id'],
                ];
            }

            return response()->json([
                'success' => true,
                'files' => $resultData,
            ], SymfonyResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error, try again later',
            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function mark(Attachment $attachment, $isFinal)
    {
        $this->attachmentRepository->mark($attachment, $isFinal);

        return response()->json([
            'success' => true,
            'message' => 'Attachment successfully marked',
        ], SymfonyResponse::HTTP_OK);
    }

    public function approve(Attachment $attachment, $isApproved)
    {
        $this->attachmentRepository->approve($attachment, $isApproved);
         //history log
        if ($isApproved) {
            $extra = ['user' => Sentinel::check()->id, 'file' => $attachment->label];
            $this->historyLogRepository->saveLog($attachment->order_id, HistoryLog::ADMIN_APPROVED_FILE, $extra);
        } else {
            $extra = ['user' => Sentinel::check()->id, 'file' => $attachment->label];
            $this->historyLogRepository->saveLog($attachment->order_id, HistoryLog::ADMIN_DISAPPROVED_FILE, $extra);
        }
        return response()->json([
            'success' => true,
            'message' => 'Attachment successfully approved',
        ], SymfonyResponse::HTTP_OK);
    }

    public function create($fileType, Request $request)
    {
        //if this is associative array
        $additionalDataForAttachment = array_merge(
            $request->only('software_id', 'report_type_id'),
            ['user_id' => Sentinel::check()->id]
        );

        $orderId = $request->session()->get('order_for_attachment_upload');

        $isComment = $request->is_comment;
        $isRework = $request->is_rework;
        $commentDetails = $request->only('content', 'namespace', 'orderId');
        $fileDetails = $request->only('software_id', 'key', 'label');
        if ($isComment === 'true') {
            $fileDetails['label'] = $request->label2;
        }
        if (array_keys($fileDetails) !== range(0, sizeof($fileDetails) - 1)) {
            $commentId = null;
            if ($isComment === 'true') {
                if ($orderId === null) {
                    $orderId = $commentDetails['orderId'];
                }

                if ($isRework === 'true') {
                    $order = Order::find($orderId);
                    $this->orderRepository->complete($order, false);
                    $order->status = Order::STATUS_SENT_BACK;
                    $order->save();
                    $commentDetails['content'] = 'I have sent back this work because is wrong: '.$commentDetails['content'];
                }
                $user = Sentinel::check();
                $data = [
                    'user_id' => Sentinel::check()->id,
                    'namespace' => $commentDetails['namespace'],
                    'parent_id' => 0,
                    'order_id' => $orderId,
                    'content' => $commentDetails['content'],
                    'role_id' => $user->roles[0]->id
                ];

                $comment = $this->commentRepository->createComments($data, $user);
                $commentId = $comment->id;

                if ($isRework === 'true') {
                    $comment->markApproved();
                    event(new ReturnOrderComment($comment->id));

                    $extra = ['user' => $user->id];
                    $this->historyLogRepository->saveLog($orderId, HistoryLog::SEND_BACK, $extra);
                }
            }
            $this->attachmentRepository->saveFile($orderId, $fileType, array_merge($fileDetails, $additionalDataForAttachment), $commentId);

            if ($isComment === 'false') {
                //history log
                $extra = ['user' => Sentinel::check()->id, 'file' => $request->label];
                $this->historyLogRepository->saveLog($orderId, HistoryLog::UPLOAD_FILE, $extra);
            }
        } else { //this is indexed array
            foreach ($fileDetails as $fileDetail) {
                $this->attachmentRepository->saveFile($orderId, $fileType, array_merge(
                    $fileDetail,
                    $additionalDataForAttachment
                ));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Attachment saved successfully',
        ], SymfonyResponse::HTTP_OK);

    }
}
