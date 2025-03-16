<?php

namespace App\Models;

use App\Enums\ChatStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ChatMessage
 *
 * Represents a chat message between two users in the application.
 *
 * @property int $id The unique identifier for the message.
 * @property int $sender_id The ID of the user who sent the message.
 * @property int $receiver_id The ID of the user who received the message.
 * @property string $content The content of the message.
 * @property ChatStatus $status The status of the message (sent, delivered, read).
 * @property \Carbon\Carbon|null $delivered_at Timestamp when the message was delivered to the receiver.
 * @property \Carbon\Carbon $created_at Timestamp when the message was created.
 * @property \Carbon\Carbon $updated_at Timestamp when the message was last updated.
 * @property-read User $sender The user who sent the message.
 * @property-read User $receiver The user who received the message.
 */
class ChatMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'status',
        'delivered_at',
        'fcm_token'
    ];

    /**
     * The attributes that should be cast to specific types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ChatStatus::class,
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the user who sent the message.
     *
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the user who received the message.
     *
     * @return BelongsTo
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Scope a query to only include messages with a specific status for a user.
     *
     * @param Builder $query The query builder instance.
     * @param int $userId The ID of the user to filter messages for.
     * @param ChatStatus $status The status to filter by.
     * @return Builder The modified query builder instance.
     */
    public function scopeWithStatus(Builder $query, int $userId, ChatStatus $status): Builder
    {
        return $query->where('receiver_id', $userId)
                     ->where('status', $status);
    }

    /**
     * Scope a query to include messages between two specific users.
     *
     * @param Builder $query The query builder instance.
     * @param int $userId1 The ID of the first user.
     * @param int $userId2 The ID of the second user.
     * @return Builder The modified query builder instance.
     */
    public function scopeBetween(Builder $query, int $userId1, int $userId2): Builder
    {
        return $query->where(function ($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId1)
              ->where('receiver_id', $userId2);
        })->orWhere(function ($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId2)
              ->where('receiver_id', $userId1);
        });
    }

    /**
     * Scope a query to include messages related to a specific user (sent or received).
     *
     * @param Builder $query The query builder instance.
     * @param int $userId The ID of the user to filter messages for.
     * @return Builder The modified query builder instance.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('sender_id', $userId)
                     ->orWhere('receiver_id', $userId);
    }

    /**
     * Scope a query to include the most recent messages.
     *
     * @param Builder $query The query builder instance.
     * @param int $limit The maximum number of recent messages to return (default: 10).
     * @return Builder The modified query builder instance.
     */
    public function scopeRecent(Builder $query, int $limit = 10): Builder
    {
        return $query->orderBy('created_at', 'desc')
                     ->limit($limit);
    }
}
