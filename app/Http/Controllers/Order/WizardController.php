<?php

namespace App\Http\Controllers\Order;

use App\Exceptions\SaveException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\User;
use App\Models\ReportType;
use App\Repositories\AttachmentRepository;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Laravel\Cashier\StripeGateway;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Repositories\UserRepository;
use App\Repositories\TransactionRepository;

class WizardController extends Controller
{
    private $orderRepository;
    private $attachmentRepository;

    public function __construct(OrderRepository $orderRepository, TransactionRepository $transactionRepository, UserRepository $userRepository, AttachmentRepository $attachmentRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function show($order)
    {
        $orderData = $this->orderRepository->getModel($order);
        $user = Sentinel::getUser();
        $subscriptionDetails = $this->userRepository->getStripeSubscriptionDetails($user->stripe_subscription, env('STRIPE_API_SECRET'));
        $subscriptionInfo = $this->transactionRepository->getSubscriptionInfo($subscriptionDetails, $user, $this->userRepository);
        $user_details = array();
        $orderDataArray = $orderData->toArray();
        $user_details['in_trial'] = $subscriptionInfo[0];
        $user_details['subscribed'] = $subscriptionInfo[1];
//        $price = $this->orderRepository->getOrderPrice($inTrial, $isSubscribed, $user->hasFreeOrders());
//        $orderDataArray['price'] = $price;
        return response()->json(array_merge(
                array_merge($orderDataArray, $user_details),
                $this->attachmentRepository->getFiles($orderData->id, $orderData->user_id)
            )
        );
    }


    public function createOrder(Request $request)
    {
        return response()->json(
            $this->orderRepository->create([
                'user_id' => $request->get('user_id'),
                'report_type_id' => $request->has('report_type_id') ? $request->get('report_type_id') : ReportType::DEFAULT_REPORT_ID,
                'software_id' => $request->has('software_id') ? $request->get('software_id') : null,
            ])
        );
    }

    /**
     * @param OrderRequest $request
     * @param                    $order
     *
     * Each files are passed in the following way:
     * [data_file_mobile] array         Newly uploaded file details
     *     [key]            string        S3 key
     *     [label]          string        label assigned to it
     * [photo]              array         List of uploaded photos
     *     [0]              array         Newly uploaded photo details
     *         [key]        string        S3 key
     *         [label]      string|int    Label assigned to it. ID of the existing label or text
     *     [0]              array         Newly uploaded photo details
     *         [key]        string        S3 key
     *         [label]      string|int    Label assigned to it. ID of the existing label or text
     *     [1]              array         Previously uploaded photo details
     *         [id]         int           ID of the attachment that was previously uploaded
     *         [label]      string|int    Label assigned to it. ID of the existing label or text
     * [comparable]         array         List of comparables
     *     [0]              array         Newly uploaded comparable details
     *         [key]        string        S3 key
     *         [address1]   string        Address line 1
     *         [address2]   string        Address line 2
     *         [state]      string        State
     *         [zip]        string        Zip code
     *     [1]              array         Previously uploaded comparable details
     *         [id]         int           ID of the attachment that was previously uploaded
     *         [address1]   string        Address line 1
     *         [address2]   string        Address line 2
     *         [state]      string        State
     *         [zip]        string        Zip code
     *
     * @return \Illuminate\Http\JsonResponse
     */
        public function saveOrder(OrderRequest $request, Order $order)
    {
        DB::beginTransaction();
        try {
            $orderId = $order->id;
            $orderFiles = $request->only($this->attachmentRepository->availableFileTypes());
            $orderData = $request->only($this->orderRepository->getFillableFields());
            foreach ($orderFiles as $fileType => $fileDetails) {
                if (!$fileDetails) {
                    continue;
                }
                //if this is associative array
                $additionalDataForAttachment = array_merge(
                    $request->only('software_id', 'report_type_id', 'user_id'),
                    ['user_id' => $order->user_id]
                );
                if (array_keys($fileDetails) !== range(0, sizeof($fileDetails) - 1)) {
                    $this->attachmentRepository->saveFile($orderId, $fileType, array_merge(
                            $fileDetails,
                            $additionalDataForAttachment
                        )
                    );
                } else { //this is indexed array
                    foreach ($fileDetails as $fileDetail) {
                        $this->attachmentRepository->saveFile($orderId, $fileType, array_merge(
                            $fileDetail,
                            $additionalDataForAttachment
                        ));
                    }
                }
            }
            $orderData = array_diff($orderData, [null]);
            $orderData['status'] = Order::STATUS_NEW;
            if (isset($orderData['effective_date'])) {
                $orderData['effective_date'] = date('Y-m-d', strtotime($orderData['effective_date']));
            }
            if (isset($orderData['report_type_id'])) {
                $orderData['report_type_id'] = (int)$orderData['report_type_id'];
            }

//            $user = Sentinel::getUser();
//            $inTrial = $this->userRepository->inTrial($user);
//            $isSubscribed = $user->subscribed();
//            $price = $this->getOrderPrice($inTrial, $isSubscribed, $user->hasFreeOrders());
//            $order->order_transaction->amount = $price;

            $order = $this->orderRepository->update($orderData, $order);

            DB::commit();

            return $this->show($order);
        } catch (SaveException $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
