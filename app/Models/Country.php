<?php

namespace App\Models;

use App\Enums\CountryAccessLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * Class Country
 *
 * Represents a country in the dating application, including its name, abbreviation,
 * flag image, and access level for user interactions. Data can be cached for performance optimization.
 *
 * @property int $id
 * @property string $name The full name of the country.
 * @property string $abbreviation The abbreviation or ISO code of the country (e.g., 'US', 'IR').
 * @property string|null $flag_image The path or URL to the country's flag image (optional).
 * @property CountryAccessLevel $access_level The access level for the country (e.g., 'free', 'banned').
 * @property \Illuminate\Support\Carbon $created_at The timestamp of when the country record was created.
 * @property \Illuminate\Support\Carbon $updated_at The timestamp of when the country record was last updated.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $bornUsers Users born in this country.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $livingUsers Users living in this country.
 */
class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'abbreviation',
        'flag_image',
        'access_level',
    ];

    /**
     * The attributes that should be cast to specific types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'access_level' => CountryAccessLevel::class,
    ];

    /**
     * Get the users whose country of birth is this country.
     *
     * @return HasMany
     */
    public function bornUsers(): HasMany
    {
        return $this->hasMany(User::class, 'born_country', 'id');
    }

    /**
     * Get the users whose country of residence is this country.
     *
     * @return HasMany
     */
    public function livingUsers(): HasMany
    {
        return $this->hasMany(User::class, 'living_country', 'id');
    }

    /**
     * Scope to filter free countries.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeFree(Builder $query): Builder
    {
        return $query->where('access_level', CountryAccessLevel::Free->value);
    }

    /**
     * Scope to filter countries requiring registration.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeRegistrationRequired(Builder $query): Builder
    {
        return $query->where('access_level', CountryAccessLevel::RegistrationRequired->value);
    }

    /**
     * Scope to filter banned countries.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeBanned(Builder $query): Builder
    {
        return $query->where('access_level', CountryAccessLevel::Banned->value);
    }

    /**
     * Scope to filter searchable-only countries.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeSearchableOnly(Builder $query): Builder
    {
        return $query->where('access_level', CountryAccessLevel::SearchableOnly->value);
    }

    /**
     * Get the cached country details with related data for performance.
     *
     * @return Country The cached country instance with loaded relations.
     */
    public function getCachedCountry(): Country
    {
        return Cache::remember("country_{$this->id}", 3600, function () {
            return $this->load('bornUsers', 'livingUsers');
        });
    }

    /**
     * Check if the country allows free access.
     *
     * @return bool Whether the country has free access.
     */
    public function isFree(): bool
    {
        return $this->access_level === CountryAccessLevel::Free;
    }

    /**
     * Check if the country is banned.
     *
     * @return bool Whether the country is banned.
     */
    public function isBanned(): bool
    {
        return $this->access_level === CountryAccessLevel::Banned;
    }

    /**
     * Check if the country requires registration for access.
     *
     * @return bool Whether the country requires registration.
     */
    public function requiresRegistration(): bool
    {
        return $this->access_level === CountryAccessLevel::RegistrationRequired;
    }

    /**
     * Check if the country is searchable only.
     *
     * @return bool Whether the country is searchable only.
     */
    public function isSearchableOnly(): bool
    {
        return $this->access_level === CountryAccessLevel::SearchableOnly;
    }

    /**
     * Invalidate the cache for this country.
     *
     * @return void
     */
    public function invalidateCache(): void
    {
        Cache::forget("country_{$this->id}");
    }
}
