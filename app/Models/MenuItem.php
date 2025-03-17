<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = ['menu_id', 'parent_id', 'label', 'route', 'icon', 'order', 'has_divider', 'roles'];

    protected $casts = [
        'has_divider' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    /**
     * Check if the current authenticated user has access to this menu item based on roles.
     *
     * @return bool
     */
    /**
     * Check if the current user has access to this menu item based on their role.
     */
    public function isAccessible(): bool
    {
        // If roles is empty, the item is accessible to everyone
        if (empty($this->roles)) {
            return true;
        }

        // Get the current user's role (could be an Enum or string)
        $userRole = auth()->user()?->role;

        // If user is not authenticated or has no role, deny access if roles are specified
        if (!$userRole) {
            return false;
        }

        // Convert the user's role to a string (handles both Enum and plain string cases)
        $userRoleValue = $userRole instanceof \App\Enums\UserRole ? $userRole->value : $userRole;

        // Split the roles string into an array and check if the user's role is included
        $allowedRoles = explode(',', $this->roles);
        return in_array($userRoleValue, $allowedRoles);
    }
}
