<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ReportTypeRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'current_price' => 'required|numeric|min:0',
            'old_price' => 'required|numeric|min:0',
        ];
    }
}
