<?php

namespace App\Policies;

use App\Support\Enums\UserAbility;

/**
 * Admin access only. Customers reach their own orders through the shop endpoints, which scope by user.
 */
class OrderPolicy extends ManagedByAbility
{
    protected function ability(): UserAbility
    {
        return UserAbility::ManageOrders;
    }
}
