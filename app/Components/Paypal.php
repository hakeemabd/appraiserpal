<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 23.01.16
 * Time: 15:09
 */
namespace App\Components;

use App\Exceptions\PaypalException;
use App\Models\Transaction;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction as PaypalTransaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class Paypal
{
    private $_api_context;

    public function __construct()
    {
        $paypal_conf = config('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function createPayment($total, $url)
    {
        /**
         * todo refactor method
         */
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item_1 = new Item();
        $item_1->setName('Order') // item name
        ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($total); // unit price

        $item_list = new ItemList();
        $item_list->setItems([$item_1]);

        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal($total);

        $transaction = new PaypalTransaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Appraiser-pal');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl($url)
            ->setCancelUrl($url);

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions([$transaction]);

        $payment->create($this->_api_context);

        return $payment;
    }

    public function getApprovalUrl(Payment $payment)
    {
        foreach($payment->getLinks() as $link) {
            if($link->getRel() == 'approval_url') {
                return $link->getHref();
            }
        }

        throw new PaypalException('approval_url');
    }

    public function executePayment($paymentId, $payerId)
    {
        $payment = Payment::get($paymentId, $this->_api_context);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        $result = $payment->execute($execution, $this->_api_context);

        if ($result->getState() == 'approved') { // payment made
            return Transaction::STATUS_PAID;
        }

        return Transaction::STATUS_OVERDUE;
    }
}