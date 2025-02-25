<?php

namespace App\Models;

use App\Enums\MediaStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Media
 *
 * Represents a media file (image, video, or audio) stored in the application.
 *
 * @property int $id
 * @property string $path The path to the media file in the system.
 * @property string $original_name The original name of the uploaded file.
 * @property string $type The type of media (image, video, audio).
 * @property string $mime_type The MIME type of the media file.
 * @property int $size The size of the media file in bytes.
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|UserMedia[] $userMedias The user-media relationships associated with this media.
 */
class Media extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'path',
        'original_name',
        'type',
        'mime_type',
        'size',
    ];

    /**
     * Get all user-media relationships associated with this media.
     *
     * @return HasMany
     */
    public function userMedias(): HasMany
    {
        return $this->hasMany(UserMedia::class, 'media_id');
    }

    /**
     * Get all users associated with this media.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function users()
    {
        return $this->hasManyThrough(User::class, UserMedia::class, 'media_id', 'id', 'id', 'user_id');
    }

    /**
     * Scope to filter media files by type (e.g., image, video, audio).
     *
     * @param Builder $query The query builder instance.
     * @param string $type The type of media to filter by.
     * @return Builder The modified query builder instance.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter media files that are approved.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereHas('userMedias', function (Builder $userMediaQuery) {
            $userMediaQuery->where('status', MediaStatus::Approved->value);
        });
    }

    /**
     * Scope to filter media files that are not approved.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeNotApproved(Builder $query): Builder
    {
        return $query->whereHas('userMedias', function (Builder $userMediaQuery) {
            $userMediaQuery->where('status', MediaStatus::NotApproved->value);
        });
    }

    /**
     * Check if the media is associated with a user's profile picture.
     *
     * @return bool
     */
    public function isProfilePicture(): bool
    {
        return $this->userMedias()->where('is_profile', true)->exists();
    }

    /**
     * Check if the media is approved.
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->userMedias()->where('status', MediaStatus::Approved->value)->exists();
    }
}
