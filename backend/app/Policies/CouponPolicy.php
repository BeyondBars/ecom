<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view coupons');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('view coupons');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create coupons');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('update coupons');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('delete coupons');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('update coupons');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('delete coupons');
    }
}
