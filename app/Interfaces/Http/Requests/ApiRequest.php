<?php

namespace App\Interfaces\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class ApiRequest extends FormRequest
{
    /**
     * Determine if user authorized to make this request
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * If validator fails return the exception in json form
     * @param Validator $validator
     * @return array
     */
    protected function failedValidation(Validator $validator)
    {
        $message = [];
        $errors_data = json_decode($validator->errors(), true);
        foreach ($errors_data as $key => $error) {
            array_push($message, [$key => $error[0]]);
        }
        throw new HttpResponseException(response()->json(['error' => 'validation', 'message' => $message], 422));
    }

    abstract public function rules();
}
