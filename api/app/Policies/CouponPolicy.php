<?php

namespace App\Policies;

use App\Support\Enums\UserAbility;

/**
 * Admin access only. Customers only use coupon codes at checkout.
 */
class CouponPolicy extends ManagedByAbility
{
    protected function ability(): UserAbility
    {
        return UserAbility::ManageCoupons;
    }
}
