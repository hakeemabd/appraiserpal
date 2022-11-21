<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkerGroupRequest extends FormRequest
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
        $group = $this->route('workerGroup');
        if ($group) {
            $sortValidation = 'required|integer|min:1|unique:groups,sort,'.$group->id;
        }
        else {
            $sortValidation = 'required|integer|min:1|unique:groups,sort';
        }
        return [
            'sort' => $sortValidation,
            'name' => 'required|between:1,250',
        ];
    }

    public function messages()
    {
        return [
            'sort.required' => 'Sequence # is required',
            'sort.min' => 'Sequence starts with 1',
        ];
    }
}
