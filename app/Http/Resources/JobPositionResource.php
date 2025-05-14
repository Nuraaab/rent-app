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
        ];
    }
}
