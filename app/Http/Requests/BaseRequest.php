<?php
namespace App\Http\Requests;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class BaseRequest extends Request {

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
            //
        ];
    }

    public function response(array $errors)
    {
        return response()->json([
            'errors' => $errors,
        ], SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

}
