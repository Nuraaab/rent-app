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
            'description'=> 'nullable|string|min:10',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'latitude'=> 'nullable|numeric|between:-90,90',
            'longitude'=> 'nullable|numeric|between:-180,180',
            'user_id'=> 'required',
            
            // Company fields
            'company_name' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
            
            // Role fields
            'employment_type' => 'nullable|string|max:50',
            'seniority' => 'nullable|string|max:50',
            'job_type'=> 'nullable|string|max:50',
            'job_salary' => 'nullable|string|max:255',
            
            // Location fields
            'work_modality' => 'nullable|string|max:50',
            'working_days' => 'nullable|string|max:255',
            'start_time' => 'nullable|string|max:20',
            'end_time' => 'nullable|string|max:20',
            'weekend_work' => 'nullable|boolean',
            
            // Compensation fields
            'pay_type' => 'nullable|string|max:50',
            'min_salary' => 'nullable|string|max:50',
            'max_salary' => 'nullable|string|max:50',
            'pay_cadence' => 'nullable|string|max:50',
            'equity' => 'nullable|string|max:255',
            'benefits' => 'nullable|string',
            
            // Requirements fields
            'experience' => 'nullable|string|max:255',
            'must_have_skills' => 'nullable|string',
            'auth_required' => 'nullable|boolean',
            
            // Screening fields
            'resume_required' => 'nullable|boolean',
            'quick_apply' => 'nullable|boolean',
            
            // Old fields (kept for backward compatibility)
            'category'=> 'nullable|string|max:255',
            'category_id'=> 'nullable|exists:categories,id',
            'client'=> 'nullable|string|max:255',
            'deadline'=> 'nullable|date',

            // Validation for job responsibilities (optional)
            'job_responsibility'=> 'nullable|array',
            'job_responsibility.*.responsiblity' => 'nullable|string|min:5',

            // Validation for job qualifications (optional)
            'job_qualification'=> 'nullable|array',
            'job_qualification.*.qualification' => 'nullable|string|min:5',
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
