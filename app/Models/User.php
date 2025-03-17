<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\MediaStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * Represents a user in the application with authentication and role-based features.
 *
 * @package App\Models
 * @property int $id The unique identifier for the user.
 * @property string $first_name The user's first name.
 * @property string|null $middle_name The user's middle name (optional).
 * @property string $last_name The user's last name.
 * @property string $display_name The user's unique display name.
 * @property Gender $gender The user's gender (male or female).
 * @property \Carbon\Carbon $birth_date The user's birth date.
 * @property string $email The user's email address.
 * @property string $phone_number The user's phone number.
 * @property string|null $father_name The user's father's name (optional).
 * @property string|null $mother_name The user's mother's name (optional).
 * @property int|null $born_country The ID of the country where the user was born.
 * @property int|null $living_country The ID of the country where the user currently resides.
 * @property \Carbon\Carbon|null $email_verified_at Timestamp of email verification.
 * @property string $password The user's hashed password.
 * @property UserRole $role The user's role (normal, admin, super_admin).
 * @property UserStatus $status The user's status (active, pending, suspended, blocked).
 * @property string|null $locale The user's preferred language (e.g., "en", "fa").
 * @property string|null $fcm_token The user's FCM token for notifications.
 * @property \Carbon\Carbon|null $last_seen Timestamp of the user's last activity.
 * @property string|null $remember_token Token for remembering user login.
 * @property \Carbon\Carbon $created_at Timestamp when the user was created.
 * @property \Carbon\Carbon $updated_at Timestamp when the user was last updated.
 * @property \Carbon\Carbon|null $deleted_at Timestamp of soft deletion.
 * @property-read Country|null $bornCountry The country where the user was born.
 * @property-read Country|null $livingCountry The country where the user currently resides.
 * @property-read \Illuminate\Database\Eloquent\Collection|UserMedia[] $media The user's media files.
 * @property-read \Illuminate\Database\Eloquent\Collection|UserMedia[] $approvedMedia The user's approved media files.
 * @property-read \Illuminate\Database\Eloquent\Collection|UserMedia[] $unapprovedMedia The user's unapproved media files.
 * @property-read UserMedia|null $profilePicture The user's profile picture.
 * @property-read \Illuminate\Database\Eloquent\Collection|UserMedia[] $photoAlbum The user's photo album.
 * @property-read \Illuminate\Database\Eloquent\Collection|ChatMessage[] $sentMessages Messages sent by the user.
 * @property-read \Illuminate\Database\Eloquent\Collection|ChatMessage[] $receivedMessages Messages received by the user.
 * @property-read \Illuminate\Database\Eloquent\Collection|Subscription[] $subscriptions The user's subscriptions.
 * @property-read \Illuminate\Database\Eloquent\Collection|Friendship[] $friendships Friendships initiated by the user.
 * @property-read \Illuminate\Database\Eloquent\Collection|Friendship[] $friendRequestsReceived Friend requests received by the user.
 * @property-read \Illuminate\Database\Eloquent\Collection|Block[] $blocks Users blocked by this user.
 * @property-read \Illuminate\Database\Eloquent\Collection|Block[] $blockedBy Users who blocked this user.
 * @property-read \Illuminate\Database\Eloquent\Collection|Report[] $reports Reports submitted by this user.
 * @property-read \Illuminate\Database\Eloquent\Collection|Report[] $reportedBy Reports against this user.
 * @property-read \Illuminate\Database\Eloquent\Collection|Friendship[] $friends Accepted friendships of the user.
 * @property-read \Illuminate\Database\Eloquent\Collection|UserAnswer[] $userAnswers Answers provided by the user.
 */
class User extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['first_name', 'middle_name', 'last_name', 'display_name', 'gender', 'birth_date', 'email', 'phone_number', 'father_name', 'mother_name', 'born_country', 'living_country', 'email_verified_at', 'password', 'role', 'status', 'locale', 'fcm_token', 'last_seen'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast to specific types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gender' => Gender::class,
        'role' => UserRole::class,
        'status' => UserStatus::class,
        'birth_date' => 'date',
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
        'last_seen' => 'datetime',
    ];

    /**
     * Get the country where the user was born.
     *
     * @return BelongsTo
     */
    public function bornCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'born_country');
    }

    /**
     * Get the country where the user currently resides.
     *
     * @return BelongsTo
     */
    public function livingCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'living_country');
    }

    /**
     * Get all media files associated with the user.
     *
     * @return HasMany
     */
    public function media(): HasMany
    {
        return $this->hasMany(UserMedia::class, 'user_id');
    }

    /**
     * Get the user's approved media files.
     *
     * @return HasMany
     */
    public function approvedMedia(): HasMany
    {
        return $this->media()->where('is_approved', true);
    }

    /**
     * Get the user's unapproved media files.
     *
     * @return HasMany
     */
    public function unapprovedMedia(): HasMany
    {
        return $this->media()->where('is_approved', false);
    }

    /**
     * Get the user's profile picture.
     *
     * @return UserMedia|null
     */
    public function profilePicture(): ?UserMedia
    {
        return $this->media()->where('is_profile', true)->first();
    }

    /**
     * Get the user's photo album (approved media excluding profile picture).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function photoAlbum()
    {
        return $this->approvedMedia()->where('is_profile', false)->orderBy('order')->get();
    }

    /**
     * Get the messages sent by the user.
     *
     * @return HasMany
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    /**
     * Get the messages received by the user.
     *
     * @return HasMany
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }

    /**
     * Get the user's subscriptions.
     *
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function latestSubscription()
    {
        return $this->subscriptions()->latest()->first();
    }

    public function activePlan()
    {
        $subscription = $this->latestSubscription();
        return $subscription?->plan;
    }
    /**
     * Get the friendships initiated by the user.
     *
     * @return HasMany
     */
    public function friendships(): HasMany
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    /**
     * Get the friend requests received by the user.
     *
     * @return HasMany
     */
    public function friendRequestsReceived(): HasMany
    {
        return $this->hasMany(Friendship::class, 'target_id');
    }

    /**
     * Get the users blocked by this user.
     *
     * @return HasMany
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'user_id');
    }

    /**
     * Get the users who blocked this user.
     *
     * @return HasMany
     */
    public function blockedBy(): HasMany
    {
        return $this->hasMany(Block::class, 'target_id');
    }

    /**
     * Get the reports submitted by this user.
     *
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'user_id');
    }

    /**
     * Get the reports against this user.
     *
     * @return HasMany
     */
    public function reportedBy(): HasMany
    {
        return $this->hasMany(Report::class, 'target_id');
    }

    /**
     * Get the accepted friendships of the user.
     *
     * @return HasMany
     */
    public function friends(): HasMany
    {
        return $this->friendships()->where('status', 'accepted')->with('target');
    }

    /**
     * Get the answers provided by the user.
     *
     * @return HasMany
     */
    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class, 'user_id');
    }

    /**
     * Scope a query to filter users with unapproved media.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeWithUnapprovedMedia(Builder $query): Builder
    {
        return $query->whereHas('media', function (Builder $mediaQuery) {
            $mediaQuery->where('status', MediaStatus::NotApproved->value);
        });
    }

    /**
     * Scope a query to filter SuperAdmin users.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeSuperAdmins(Builder $query): Builder
    {
        return $query->where('role', UserRole::SuperAdmin->value);
    }

    /**
     * Scope a query to filter Admin users.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', UserRole::Admin->value);
    }

    /**
     * Scope a query to filter Normal users.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeNormals(Builder $query): Builder
    {
        return $query->where('role', UserRole::Normal->value);
    }

    /**
     * Scope a query to filter active users.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Active->value);
    }

    /**
     * Scope a query to filter pending users.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Pending->value);
    }

    /**
     * Scope a query to filter suspended users.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeSuspended(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Suspended->value);
    }

    /**
     * Scope a query to filter blocked users.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeBlocked(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Blocked->value);
    }

    /**
     * Check if the user has the SuperAdmin role.
     *
     * @return bool True if the user is a SuperAdmin, false otherwise.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    /**
     * Check if the user has the Admin role.
     *
     * @return bool True if the user is an Admin, false otherwise.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Check if the user has the Normal role.
     *
     * @return bool True if the user is a Normal user, false otherwise.
     */
    public function isNormal(): bool
    {
        return $this->role === UserRole::Normal;
    }

    /**
     * Check if the user account is active.
     *
     * @return bool True if the user is active, false otherwise.
     */
    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    /**
     * Check if the user account is pending.
     *
     * @return bool True if the user is pending, false otherwise.
     */
    public function isPending(): bool
    {
        return $this->status === UserStatus::Pending;
    }

    /**
     * Check if the user account is suspended.
     *
     * @return bool True if the user is suspended, false otherwise.
     */
    public function isSuspended(): bool
    {
        return $this->status === UserStatus::Suspended;
    }

    /**
     * Check if the user account is blocked.
     *
     * @return bool True if the user is blocked, false otherwise.
     */
    public function isBlocked(): bool
    {
        return $this->status === UserStatus::Blocked;
    }
}
