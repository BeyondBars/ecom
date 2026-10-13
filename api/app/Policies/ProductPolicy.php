<?php

namespace App\Policies;

use App\Support\Enums\UserAbility;

class ProductPolicy extends ManagedByAbility
{
    protected function ability(): UserAbility
    {
        return UserAbility::ManageCatalog;
    }
}
