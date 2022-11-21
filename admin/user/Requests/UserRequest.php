<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 12/26/15
 * Time: 10:34 PM
 */

namespace Admin\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $user = $this->route('user');
        if ($user) {
            $emailValidation = 'required|email|unique:users,email,'.$user->id;
        }
        else {
            $emailValidation = 'required|email|unique:users,email';
        }
        return [
            'email' => $emailValidation,
            'password' => 'between:4,20',
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'mobile_phone' => 'required|max:15',
            'work_phone' => 'max:15',
            'address_line_1' => 'sometimes|required|max:200',
            'address_line_2' => 'max:200',
            'state' => 'sometimes|required',
            'zip' => 'digits_between:5,10',
            'license_number' => 'sometimes|required|digits:10',
            'auto_comments' => 'sometimes|required',
            'auto_orders' => 'sometimes|required',
            'paypal_email' => 'email',
            'bank_name' => 'max:100',
            'account_number' => 'digits_between:10,12',
            'routing_number' => 'digits:9',
        ];
    }
}