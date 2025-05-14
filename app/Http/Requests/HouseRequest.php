<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HouseRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'required|string|max:1000',
            'max_number_of_gusts' => 'required|integer|min:1',
            'number_of_bedrooms' => 'required|integer|min:0',
            'number_of_baths' => 'required|integer|min:0',
            'phone_number' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longtiude' => 'required|numeric|between:-180,180',
            'price' => 'required|numeric|min:0',
            'check_in_time' => 'required',
            'check_out_time' => 'required',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'user_id' => 'required|integer|exists:users,id',

            'house_offers' => 'nullable', 
            'house_offers.*.offer_name' => 'required_with:house_offers|string|max:255',

            'images' => 'required|array|min:1', 
            'images.*.image_url' => 'required|url', 
        ];

    }

    public function messages()
    {
        return [
            'phone_number.regex' => 'The phone number must be a valid international format (e.g., +1234567890).',
            'end_date.after' => 'End date must be after the start date.',
            'images.required' => 'At least one image is required.',
            'description.max' => 'The description must not exceed 1000 characters.'
        ];
    }
}
