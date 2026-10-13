<?php

namespace App\Policies;

use App\Support\Enums\UserAbility;

class CategoryPolicy extends ManagedByAbility
{
    protected function ability(): UserAbility
    {
        return UserAbility::ManageCatalog;
    }
}
