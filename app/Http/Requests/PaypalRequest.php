<?php

namespace App\Http\Requests;

class PaypalRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'PayerID' => 'required',
            'token' => 'required',
        ];
    }
}
