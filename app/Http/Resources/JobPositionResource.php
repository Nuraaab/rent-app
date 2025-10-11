<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class JobPositionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
    
        return [
            'id' =>$this->id,
            'job_responsibility' =>$this->responsiblity->map(function ($resp) {
              return new ResponsiblityResource($resp);
                }),
            'job_qualification' =>$this->qualification->map(function ($qual) {
            return new QualificationResource($qual);
            }),
            'title' => $this->title,
            'job_salary' => $this->job_salary,
            'job_type' =>  $this->job_type,
            'client' => $this->client,
            'deadline' => $this->deadline,
            'description' => $this->description,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'category' => $this->category,
            'category_type' => 'Job',
            'user_id' => $this->user->id,
            'created_at' => $this->created_at,
            // New fields
            'company_name' => $this->company_name,
            'contact_name' => $this->contact_name,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'website' => $this->website,
            'employment_type' => $this->employment_type,
            'seniority' => $this->seniority,
            'work_modality' => $this->work_modality,
            'working_days' => $this->working_days,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'weekend_work' => $this->weekend_work,
            'pay_type' => $this->pay_type,
            'min_salary' => $this->min_salary,
            'max_salary' => $this->max_salary,
            'pay_cadence' => $this->pay_cadence,
            'equity' => $this->equity,
            'benefits' => $this->benefits,
            'experience' => $this->experience,
            'must_have_skills' => $this->must_have_skills,
            'auth_required' => $this->auth_required,
            'resume_required' => $this->resume_required,
            'quick_apply' => $this->quick_apply,
        ];
    }
}
