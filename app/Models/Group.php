<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'privacy',
        'meeting_type',
        'city',
        'state',
        'zip_code',
        'online_meeting_url',
        'start_date',
        'start_time',
        'end_time',
        'timezone',
        'repeat',
        'group_banner_image',
        'admin_approval',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'admin_approval' => 'boolean',
    ];

    protected $appends = [
        'member_count',
        'is_joined',
        'next_meeting',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * Get the user who created the group.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the members of the group.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    /**
     * Get the member count attribute.
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }

    /**
     * Get the is_joined attribute.
     */
    public function getIsJoinedAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        
        return $this->members()->where('user_id', auth()->id())->exists();
    }

    /**
     * Get the next meeting date based on repeat frequency.
     */
    public function getNextMeetingAttribute(): ?string
    {
        $startDate = $this->start_date;
        $now = now();
        
        if ($startDate->isPast()) {
            switch ($this->repeat) {
                case 'Daily':
                    $nextMeeting = $startDate->addDays(ceil($now->diffInDays($startDate)));
                    break;
                case 'Weekly':
                    $nextMeeting = $startDate->addWeeks(ceil($now->diffInWeeks($startDate)));
                    break;
                case 'Monthly':
                    $nextMeeting = $startDate->addMonths(ceil($now->diffInMonths($startDate)));
                    break;
                default:
                    return null;
            }
        } else {
            $nextMeeting = $startDate;
        }
        
        return $nextMeeting->format('Y-m-d');
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        if ($category && $category !== 'All') {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Scope to filter by meeting type.
     */
    public function scopeByMeetingType($query, $meetingType)
    {
        return $query->where('meeting_type', $meetingType);
    }

    /**
     * Scope to search groups.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to get groups joined by a user.
     */
    public function scopeJoinedBy($query, $userId)
    {
        return $query->whereHas('members', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Check if a user is a member of this group.
     */
    public function hasMember($userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    /**
     * Add a member to the group.
     */
    public function addMember($userId): bool
    {
        if (!$this->hasMember($userId)) {
            $this->members()->attach($userId, ['joined_at' => now()]);
            return true;
        }
        return false;
    }

    /**
     * Remove a member from the group.
     */
    public function removeMember($userId): bool
    {
        if ($this->hasMember($userId)) {
            $this->members()->detach($userId);
            return true;
        }
        return false;
    }
}
