<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy for managing access to User model operations.
 *
 * This policy defines authorization rules for interacting with users, restricting management to admins.
 *
 * @category Policies
 * @package  App\Policies
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     *
     * Only Admins and SuperAdmins can view all users.
     *
     * @param  User  $user The authenticated user.
     * @return Response Authorization response.
     */
    public function viewAny(User $user): Response
    {
        return in_array($user->role, [UserRole::Admin, UserRole::SuperAdmin])
            ? Response::allow()
            : Response::deny('Only admins can view the user list.');
    }

    /**
     * Determine whether the user can view a specific user.
     *
     * Users can view their own profile; Admins and SuperAdmins can view any user.
     *
     * @param  User  $user The authenticated user.
     * @param  User  $target The user being viewed.
     * @return Response Authorization response.
     */
    public function view(User $user, User $target): Response
    {
        return $user->id === $target->id || in_array($user->role, [UserRole::Admin, UserRole::SuperAdmin])
            ? Response::allow()
            : Response::deny('You can only view your own profile.');
    }

    /**
     * Determine whether the user can update a user.
     *
     * Only Admins and SuperAdmins can update users.
     *
     * @param  User  $user The authenticated user.
     * @param  User  $target The user being updated.
     * @return Response Authorization response.
     */
    public function update(User $user, User $target): Response
    {
        return in_array($user->role, [UserRole::Admin, UserRole::SuperAdmin])
            ? Response::allow()
            : Response::deny('Only admins can update users.');
    }

    /**
     * Determine whether the user can delete a user.
     *
     * Only Admins and SuperAdmins can delete users, except themselves.
     *
     * @param  User  $user The authenticated user.
     * @param  User  $target The user being deleted.
     * @return Response Authorization response.
     */
    public function delete(User $user, User $target): Response
    {
        return $user->id !== $target->id && in_array($user->role, [UserRole::Admin, UserRole::SuperAdmin])
            ? Response::allow()
            : Response::deny('Only admins can delete users, and you cannot delete yourself.');
    }

    /**
     * Determine whether the user can manage users (general admin access).
     *
     * @param  User  $user The authenticated user.
     * @return Response Authorization response.
     */
    public function manage(User $user): Response
    {
        return in_array($user->role, [UserRole::Admin, UserRole::SuperAdmin])
            ? Response::allow()
            : Response::deny('You do not have permission to manage users.');
    }
}
