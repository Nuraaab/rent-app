<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobRequest extends FormRequest
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
            'title'=> 'required|string|max:255',
            'category'=> 'required|string|max:255',
            'job_salary' => 'required|numeric|min:0',
            'job_type'=> 'required|string|in:Full-time,Part-time,Contract,Internship',
            'client'=> 'required|string|max:255',
            'deadline'=> 'required|date|after_or_equal:today',
            'description'=> 'required|string|min:10',
            'phone_number' => 'required|regex:/^(\+?\d{1,3}[- ]?)?\d{10}$/',
            'address' => 'required|string|max:255',
            'latitude'=> 'required|numeric|between:-90,90',
            'longitude'=> 'required|numeric|between:-180,180',
            'user_id'=> 'required|exists:users,id',

            // Validation for job responsibilities
            'job_responsibility'=> 'required|array|min:1',
            'job_responsibility.*.responsiblity' => 'required|string|min:5',

            // Validation for job qualifications
            'job_qualification'=> 'required|array|min:1',
            'job_qualification.*.qualification' => 'required|string|min:5',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The job title is required.',
            'job_salary.numeric' => 'The salary must be a number.',
            'job_salary.min' => 'The salary must be at least 0.',
            'job_type.in' => 'The job type must be Full-time, Part-time, Contract, or Internship.',
            'deadline.after_or_equal' => 'The deadline must be today or a future date.',
            'phone_number.regex' => 'Invalid phone number format.',
            'latitude.between' => 'Invalid latitude value.',
            'longitude.between' => 'Invalid longitude value.',
            'job_responsibility.required' => 'At least one job responsibility is required.',
            'job_responsibility.*.responsiblity.required' => 'Each responsibility must have a description.',
            'job_qualification.required' => 'At least one job qualification is required.',
            'job_qualification.*.qualification.required' => 'Each qualification must have a description.',
        ];
    }
}
