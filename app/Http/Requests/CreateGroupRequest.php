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
        // If route has a group parameter, this request is being used for update.
        // Existing groups can have historical start dates, so don't force >= today on edit.
        $isUpdate = $this->route('group') !== null;

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category' => 'nullable|string|max:255',
            'privacy' => 'nullable|string|in:open,closed',
            'meeting_type' => 'nullable|string|in:In Person,Online',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'online_meeting_url' => 'nullable|url|max:500',
            'start_date' => $isUpdate
                ? 'nullable|date'
                : 'nullable|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'timezone' => 'nullable|string|max:255',
            'repeat' => 'nullable|string|max:255',
            'admin_approval' => 'nullable|boolean',
            'group_banner_image' => 'nullable|string|url',
            'cover_image' => 'nullable|string|url',
            'height' => 'nullable|string|max:20',
            'pets' => 'nullable|string|in:cat,dog,other',
            'children' => 'nullable|string|in:dont_want,want_someday,have_and_want_more,have_and_dont_want_more',
            'politics' => 'nullable|string|in:liberal,moderate,conservative,other',
            'faith_identity' => 'nullable|string|in:agnostic,anglican,baptist,catholic,church_of_christ,episcopalian,evangelical,lutheran,methodist,nazarene,non_denominational,orthodox,pentecostal,presbyterian,spiritual_but_not_religious,not_sure_yet,other',
            'education' => 'nullable|string|in:high_school,some_college,associates_degree,graduate_degree,phd_post_doctoral',
            'body_type' => 'nullable|string|in:fit,curvy,slim,chubby',
            'exercise' => 'nullable|string|in:never,sometimes,often',
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
            'category.in' => 'Invalid category selected',
            'privacy.in' => 'Privacy must be either open or closed',
            'meeting_type.in' => 'Invalid meeting type selected',
            'online_meeting_url.url' => 'Please provide a valid URL',
            'start_date.after_or_equal' => 'Start date must be today or later',
            'start_time.date_format' => 'Invalid start time format',
            'end_time.date_format' => 'Invalid end time format',
            'admin_approval.boolean' => 'Admin approval must be true or false',
        ];
    }
}
