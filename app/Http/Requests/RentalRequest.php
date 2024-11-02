<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RentalRequest extends FormRequest
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
        'title' => 'required',
        'description' => 'required',
        'category_id' => 'required',
        'max_number_of_gusts' => 'required',
        'number_of_bedrooms' => 'required',
        'number_of_baths' => 'required',
        'phone_number' => 'required',
        'address' => 'required',
        'latitude' => 'required',
        'longtiude' => 'required',
        'price' => 'required',
        'check_in_time' =>'required',
        'check_out_time' =>'required',
        'start_date' => 'required',
        'end_date' => 'required',
        'user_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'user id required',
        ];
    }
}
