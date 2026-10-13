<?php

namespace App\Policies;

use App\Support\Enums\UserAbility;

class BrandPolicy extends ManagedByAbility
{
    protected function ability(): UserAbility
    {
        return UserAbility::ManageCatalog;
    }
}
