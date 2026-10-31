<?php

namespace App\Policies;

use App\Support\Enums\UserAbility;

class ContactMessagePolicy extends ManagedByAbility
{
    protected function ability(): UserAbility
    {
        return UserAbility::ManageContent;
    }
}
