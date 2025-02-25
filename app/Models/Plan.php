<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Plan
 *
 * Represents a subscription plan in the application, defining available features and pricing options.
 *
 * @property int $id
 * @property string $name The name of the plan (e.g., "A", "B", "C").
 * @property string|null $description A description of the plan (optional).
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Feature[] $features The features included in this plan.
 * @property-read \Illuminate\Database\Eloquent\Collection|PlanPrice[] $prices The pricing options for this plan.
 * @property-read \Illuminate\Database\Eloquent\Collection|Subscription[] $subscriptions The subscriptions associated with this plan.
 */
class Plan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the features included in this plan.
     *
     * @return BelongsToMany
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'plan_features', 'plan_id', 'feature_id');
    }

    /**
     * Get the pricing options for this plan.
     *
     * @return HasMany
     */
    public function prices(): HasMany
    {
        return $this->hasMany(PlanPrice::class, 'plan_id');
    }

    /**
     * Get the subscriptions associated with this plan.
     *
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}
