<?php

namespace App\Http\Requests;


class RecoverRequest extends GuestCustomerRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|exists:users'
        ];
    }
}
