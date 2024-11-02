<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "first_name" => "required",
            "last_name" => "required",
            'email' => 'required|email|unique:users',
            'password' => 'required_with:confirm_password|same:confirm_password|string|min:4',  
            "phone_number" => 'required',
            "profile_image_path" => 'required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        $statusCode = 400;

        throw new ValidationException($validator, $this->response($errors, $statusCode));
    }

    public function response(array $errors, int $statusCode = 422)
    {
        return response()->json($errors, $statusCode);
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Please Enter Name',
        ];
    }
}
