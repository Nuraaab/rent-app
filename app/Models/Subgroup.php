<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subgroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
        'description',
        'created_by',
    ];

    /**
     * Get the group that owns the subgroup.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user who created the subgroup.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
