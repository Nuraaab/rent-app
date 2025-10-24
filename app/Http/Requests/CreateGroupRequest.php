<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
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
            'description' => 'required|string|max:1000',
            'category' => 'required|string|in:Art & Culture,Career & Business,Food & Drink,Health & Wellness,Hobbies & Passion,Learning & Education,Social Activities,Sport & Fitness,Technology,Travel & Adventure',
            'meeting_type' => 'required|string|in:In Person,Online',
            'city' => 'required_if:meeting_type,In Person|string|max:255',
            'state' => 'required_if:meeting_type,In Person|string|max:255',
            'zip_code' => 'required_if:meeting_type,In Person|string|max:10',
            'online_meeting_url' => 'required_if:meeting_type,Online|url|max:500',
            'start_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'timezone' => 'required|string|in:Eastern,Central,Mountain,Pacific,UTC',
            'repeat' => 'required|string|in:None,Daily,Weekly,Monthly',
            'admin_approval' => 'required|boolean',
            'group_banner_image' => 'nullable|string|url',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Group title is required',
            'title.max' => 'Group title must not exceed 255 characters',
            'description.required' => 'Group description is required',
            'description.max' => 'Group description must not exceed 1000 characters',
            'category.required' => 'Please select a category',
            'category.in' => 'Invalid category selected',
            'meeting_type.required' => 'Please select a meeting type',
            'meeting_type.in' => 'Invalid meeting type selected',
            'city.required_if' => 'City is required for in-person meetings',
            'state.required_if' => 'State is required for in-person meetings',
            'zip_code.required_if' => 'Zip code is required for in-person meetings',
            'online_meeting_url.required_if' => 'Online meeting URL is required for online meetings',
            'online_meeting_url.url' => 'Please provide a valid URL',
            'start_date.required' => 'Start date is required',
            'start_date.after_or_equal' => 'Start date must be today or later',
            'start_time.required' => 'Start time is required',
            'start_time.date_format' => 'Invalid start time format',
            'end_time.required' => 'End time is required',
            'end_time.date_format' => 'Invalid end time format',
            'end_time.after' => 'End time must be after start time',
            'timezone.required' => 'Please select a timezone',
            'timezone.in' => 'Invalid timezone selected',
            'repeat.required' => 'Please select a repeat frequency',
            'repeat.in' => 'Invalid repeat frequency selected',
            'admin_approval.required' => 'Admin approval setting is required',
            'admin_approval.boolean' => 'Admin approval must be true or false',
            'group_banner_image.image' => 'Banner must be an image file',
            'group_banner_image.mimes' => 'Banner must be a JPEG, PNG, JPG, or GIF file',
            'group_banner_image.max' => 'Banner image must not exceed 2MB',
        ];
    }
}
