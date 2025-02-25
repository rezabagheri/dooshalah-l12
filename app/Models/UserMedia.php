<?php

namespace App\Models;

use App\Enums\MediaStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserMedia
 *
 * Represents the relationship between a user and their media files, including profile pictures and photo albums.
 *
 * @property int $id
 * @property int $user_id The ID of the user who owns this media.
 * @property int $media_id The ID of the media file.
 * @property bool $is_profile Indicates if this is the user's profile picture.
 * @property MediaStatus $status The approval status of the media (approved or not_approved).
 * @property int $order The order of this media in the user's photo album.
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user The user who owns this media.
 * @property-read Media $media The associated media file.
 */
class UserMedia extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_medias';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'media_id',
        'is_profile',
        'is_approved',
        'order',
    ];

    /**
     * The attributes that should be cast to specific types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_profile' => 'boolean',
        'is_approved' => 'boolean',
    ];
    /**
     * Get the user who owns this media.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the media file associated with this relationship.
     *
     * @return BelongsTo
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
