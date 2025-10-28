<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserInteraction;
use App\Models\NudgeUsage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'profile_image_path',
        'firebase_uid'
    ];
    public function job(){
         return $this->hasMany(Job::class);
    }

    public function rentals(){
        return $this->hasMany(Rental::class);
      }
   public function review(){
    return $this->hasMany(Review::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function joinedGroups()
    {
        return $this->belongsToMany(Group::class, 'group_members');
    }

    /**
     * Get interactions sent by this user.
     */
    public function userInteractions()
    {
        return $this->hasMany(UserInteraction::class, 'user_id');
    }

    /**
     * Get interactions received by this user.
     */
    public function receivedInteractions()
    {
        return $this->hasMany(UserInteraction::class, 'target_user_id');
    }

    /**
     * Get nudge usage record for this user.
     */
    public function nudgeUsage()
    {
        return $this->hasOne(NudgeUsage::class);
    }

    /**
     * Get networking profiles created by this user.
     */
    public function networkingProfiles()
    {
        return $this->hasMany(NetworkingProfile::class);
    }

    /**
     * Get networking connections made by this user.
     */
    public function networkingConnections()
    {
        return $this->hasMany(NetworkingConnection::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get conversations where this user is user1
     */
    public function conversationsAsUser1()
    {
        return $this->hasMany(Conversation::class, 'user1_id');
    }

    /**
     * Get conversations where this user is user2
     */
    public function conversationsAsUser2()
    {
        return $this->hasMany(Conversation::class, 'user2_id');
    }

    /**
     * Get all conversations for this user
     */
    public function conversations()
    {
        return Conversation::where('user1_id', $this->id)
            ->orWhere('user2_id', $this->id)
            ->orderBy('last_message_at', 'desc');
    }

    /**
     * Get messages sent by this user
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
