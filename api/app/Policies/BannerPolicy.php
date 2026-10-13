<?php

namespace App\Policies;

use App\Support\Enums\UserAbility;

class BannerPolicy extends ManagedByAbility
{
    protected function ability(): UserAbility
    {
        return UserAbility::ManageContent;
    }
}
