<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Subscription
 *
 * Represents a user's subscription to a plan in the application, including the price paid and duration details.
 *
 * @property int $id
 * @property int $user_id The ID of the user who subscribed.
 * @property int $plan_id The ID of the subscribed plan.
 * @property int $plan_price_id The ID of the price details at the time of purchase.
 * @property float $amount The amount paid for the subscription.
 * @property \Carbon\Carbon $start_date Timestamp when the subscription starts.
 * @property \Carbon\Carbon $end_date Timestamp when the subscription ends.
 * @property string $status The status of the subscription (active, expired, canceled).
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user The user who owns this subscription.
 * @property-read Plan $plan The plan associated with this subscription.
 * @property-read PlanPrice $planPrice The price details of this subscription.
 * @property-read \Illuminate\Database\Eloquent\Collection|Payment[] $payments Payments made for this subscription.
 */
class Subscription extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'plan_id',
        'plan_price_id',
        'amount',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * The attributes that should be cast to specific types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Get the user who owns this subscription.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan associated with this subscription.
     *
     * @return BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the price details of this subscription.
     *
     * @return BelongsTo
     */
    public function planPrice(): BelongsTo
    {
        return $this->belongsTo(PlanPrice::class, 'plan_price_id');
    }

    /**
     * Get all payments made for this subscription.
     *
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'subscription_id');
    }

    /**
     * Check if the subscription is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && now()->between($this->start_date, $this->end_date);
    }

    /**
     * Check if the subscription has expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || now()->greaterThan($this->end_date);
    }
}
