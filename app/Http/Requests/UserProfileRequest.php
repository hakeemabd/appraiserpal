<?php

namespace App\Http\Requests;

class UserProfileRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'mobile_phone' => 'required|max:25',
            'work_phone' => 'string|max:25',
            'address_line_1' => 'required|max:100',
            'address_line_2' => 'max:100',
            'state' => 'required',
            'city' => 'required|max:25',
            'zip' => 'required|max:10',
            'license_number' => 'required|max:10',
            'standard_instructions' => 'required|max:65000',
        ];
    }
}