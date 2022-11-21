<?php

namespace App\Http\Controllers;

use App\Exceptions\PromoCodeException;
use App\Models\PromoCode;
use App\Models\Transaction;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use yajra\Datatables\Datatables;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\PromoCodeRepository;
use App\Repositories\TransactionRepository;
use App\Http\Requests\PromoCodeRequest;

class PromoCodeController extends Controller
{

    private $transactionRepository;
    private $promoCodeRepository;

    public function __construct(TransactionRepository $transactionRepository,
                                PromoCodeRepository $promoCodeRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->promoCodeRepository = $promoCodeRepository;
    }

    public function applyCode(Transaction $transaction, PromoCode $promoCode)
    {
        if (!$promoCode->canApplyCode()) {
            throw new PromoCodeException('You can\'t apply this code');
        }
        if (!$transaction->hasPromoCode()) {
            throw new PromoCodeException('You\'ve used the promo code');
        }

        $this->promoCodeRepository->update([
            'count' => --$promoCode->count,
        ], $promoCode->id);

        return response()->json(
            $this->transactionRepository->update([
                'amount' => $transaction->amount * (100 - $promoCode->percent) / 100,
                'promo_code_id' => $promoCode->id,
            ], $transaction->id)
        );
    }

    public function view()
    {
        if (!empty(Sentinel::check()) && Sentinel::check()->inRole('administrator')) {
            return view('order.promo-code.index');
        }
        return url('/');
    }

    public function getPromoCodes()
    {
        return Datatables::of($this->promoCodeRepository->all())
            ->addRowData('actions', function ($model) {
                $actions = [];

                $actions['edit'] = [
                    'link' => route('admin:promoCode.update', ['report_type' => $model->id]),
                    'ajax' => false,
                ];

                $actions['delete'] = [
                    'link' => route('admin:promoCode.delete', ['id' => $model->id]),
                ];

                return $actions;
            })
            ->make(true);
    }

    public function destroy($id)
    {
        return response()->json(
            $this->promoCodeRepository->delete($id)
        );
    }

    public function showForm(PromoCode $promoCode)
    {
        return view('order.promo-code.promoCodeForm', compact('promoCode'));
    }

    public function store(PromoCodeRequest $request, $id = false)
    {
        $promoCodeData = $request->only(['code', 'percent', 'count']);

        if ($id) {
            return response()->json(
                $this->promoCodeRepository->update($promoCodeData, $id)
            );
        }

        return response()->json(
            $this->promoCodeRepository->create($promoCodeData)
        );
    }
}
