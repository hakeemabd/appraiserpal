<?php

namespace App\Http\Requests;

class RegistrationRequest extends GuestCustomerRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users,email',
            'mobile_phone' => 'required|max:15',
            'password' => 'required|between:4,20',
            'password_confirmation' => 'same:password',
        ];
    }
}
