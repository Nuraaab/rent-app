<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosition extends Model
{
    use HasFactory;
    protected $fillable=[
        'title',
        'category',
        'category_id',
        'job_salary',
        'job_type',
        'client',
        'deadline',
        'description',
        'phone_number',
        'address',
        'latitude',
        'longitude',
        'user_id',
        // Company fields
        'company_name',
        'contact_name',
        'contact_email',
        'contact_phone',
        'website',
        // Role fields
        'employment_type',
        'seniority',
        // Location fields
        'work_modality',
        'working_days',
        'start_time',
        'end_time',
        'weekend_work',
        // Compensation fields
        'pay_type',
        'min_salary',
        'max_salary',
        'pay_cadence',
        'equity',
        'benefits',
        // Requirements fields
        'experience',
        'must_have_skills',
        'auth_required',
        // Screening fields
        'resume_required',
        'quick_apply',
    ];
    public function category(){
      return $this->belongsTo(Category::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
      }

      public function responsiblity(){
        return $this->hasMany(Responsiblity::class);
   }

      public function qualification(){
        return $this->hasMany(Qualification::class);
      }
}
