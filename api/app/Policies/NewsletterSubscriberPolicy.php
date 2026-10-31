<?php

namespace App\Policies;

use App\Support\Enums\UserAbility;

class NewsletterSubscriberPolicy extends ManagedByAbility
{
    protected function ability(): UserAbility
    {
        return UserAbility::ManageContent;
    }
}
