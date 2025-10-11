<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_positions', function (Blueprint $table) {
            // Company fields
            $table->string('company_name')->nullable()->after('category_id');
            $table->string('contact_name')->nullable()->after('company_name');
            $table->string('contact_email')->nullable()->after('contact_name');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->string('website')->nullable()->after('contact_phone');
            
            // Role fields
            $table->string('employment_type')->nullable()->after('job_type'); // full_time, part_time, contract, internship
            $table->string('seniority')->nullable()->after('employment_type'); // junior, mid, senior
            
            // Location fields
            $table->string('work_modality')->nullable()->after('address'); // onsite, remote, hybrid
            $table->string('working_days')->nullable()->after('work_modality'); // Mon,Tue,Wed,Thu,Fri
            $table->string('start_time')->nullable()->after('working_days'); // 9:00 AM
            $table->string('end_time')->nullable()->after('start_time'); // 5:00 PM
            $table->boolean('weekend_work')->default(false)->after('end_time');
            
            // Compensation fields
            $table->string('pay_type')->nullable()->after('job_salary'); // hourly, salary
            $table->string('min_salary')->nullable()->after('pay_type');
            $table->string('max_salary')->nullable()->after('min_salary');
            $table->string('pay_cadence')->nullable()->after('max_salary'); // hour, week, month, year
            $table->string('equity')->nullable()->after('pay_cadence');
            $table->text('benefits')->nullable()->after('equity'); // JSON array of benefits
            
            // Requirements fields
            $table->string('experience')->nullable()->after('description'); // e.g., "2-4 years"
            $table->text('must_have_skills')->nullable()->after('experience'); // JSON array
            $table->boolean('auth_required')->default(false)->after('must_have_skills');
            
            // Screening fields
            $table->boolean('resume_required')->default(true)->after('auth_required');
            $table->boolean('quick_apply')->default(false)->after('resume_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_positions', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'contact_name',
                'contact_email',
                'contact_phone',
                'website',
                'employment_type',
                'seniority',
                'work_modality',
                'working_days',
                'start_time',
                'end_time',
                'weekend_work',
                'pay_type',
                'min_salary',
                'max_salary',
                'pay_cadence',
                'equity',
                'benefits',
                'experience',
                'must_have_skills',
                'auth_required',
                'resume_required',
                'quick_apply',
            ]);
        });
    }
};
