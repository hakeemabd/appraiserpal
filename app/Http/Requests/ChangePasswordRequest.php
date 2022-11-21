<?php

namespace App\Http\Requests;

class ChangePasswordRequest extends GuestCustomerRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users',
            'code' => 'required',
            'password' => 'required|between:4,20',
            'password_confirmation' => 'same:password',
        ];
    }
}
