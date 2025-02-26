<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy for managing access to Plan model operations.
 *
 * This policy defines authorization rules for interacting with subscription plans.
 *
 * @category Policies
 * @package  App\Policies
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
class PlanPolicy
{
    /**
     * Determine whether the user can view any plans.
     *
     * All authenticated users can view plans.
     *
     * @param  User  $user The authenticated user.
     * @return Response Authorization response.
     */
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view a specific plan.
     *
     * All authenticated users can view individual plans.
     *
     * @param  User  $user The authenticated user.
     * @param  Plan  $plan The plan being viewed.
     * @return Response Authorization response.
     */
    public function view(User $user, Plan $plan): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create plans.
     *
     * Only Admins and SuperAdmins can create plans.
     *
     * @param  User  $user The authenticated user.
     * @return Response Authorization response.
     */
    public function create(User $user): Response
    {
        return in_array($user->role, [UserRole::Admin, UserRole::SuperAdmin])
            ? Response::allow()
            : Response::deny('Only admins can create plans.');
    }

    /**
     * Determine whether the user can update a plan.
     *
     * Only Admins and SuperAdmins can update plans.
     *
     * @param  User  $user The authenticated user.
     * @param  Plan  $plan The plan being updated.
     * @return Response Authorization response.
     */
    public function update(User $user, Plan $plan): Response
    {
        return in_array($user->role, [UserRole::Admin, UserRole::SuperAdmin])
            ? Response::allow()
            : Response::deny('Only admins can update plans.');
    }

    /**
     * Determine whether the user can delete a plan.
     *
     * Only Admins and SuperAdmins can delete plans.
     *
     * @param  User  $user The authenticated user.
     * @param  Plan  $plan The plan being deleted.
     * @return Response Authorization response.
     */
    public function delete(User $user, Plan $plan): Response
    {
        return in_array($user->role, [UserRole::Admin, UserRole::SuperAdmin])
            ? Response::allow()
            : Response::deny('Only admins can delete plans.');
    }

    /**
     * Determine whether the user can manage plans (general admin access).
     *
     * @param  User  $user The authenticated user.
     * @return Response Authorization response.
     */
    public function manage(User $user): Response
    {
        return in_array($user->role, [UserRole::Admin, UserRole::SuperAdmin])
            ? Response::allow()
            : Response::deny('You do not have permission to manage plans.');
    }
}
