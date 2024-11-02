<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HouseOfferRequest extends FormRequest
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
            "offer_name" => "required",
            "rental_id" =>"required"
        ];
    }
    public function messages()
    {
        return [
            'offer_name.required' => 'House Offer Name required',
        ];
    }
}
