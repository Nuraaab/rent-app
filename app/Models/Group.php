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
        'cover_image',
        'privacy',
        'created_by',
        'member_count',
    ];

    protected $casts = [
        'privacy' => 'string',
        'member_count' => 'integer',
    ];

    protected $appends = [
        'is_joined',
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
                    ->withPivot('joined_at', 'status', 'approved_by')
                    ->withTimestamps();
    }

    /**
     * Get the subgroups of the group.
     */
    public function subgroups(): HasMany
    {
        return $this->hasMany(Subgroup::class);
    }

    /**
     * Get the posts of the group.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the is_joined attribute.
     */
    public function getIsJoinedAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        
        return $this->members()
                    ->where('user_id', auth()->id())
                    ->wherePivot('status', 'active')
                    ->exists();
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
     * Scope to filter by privacy.
     */
    public function scopeByPrivacy($query, $privacy)
    {
        return $query->where('privacy', $privacy);
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
