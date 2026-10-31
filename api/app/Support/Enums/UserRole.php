<?php

namespace App\Support\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case User = 'user';
    case Staff = 'staff';

    public function title(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::User => 'Customer',
            self::Staff => 'Staff',
        };
    }

    public function desc(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Has full access to all system features, settings, and user management.',
            self::User => 'Customer: shops on the storefront and manages their own orders, addresses and profile.',
            self::Staff => 'Internal team member with access to operational tools and limited administrative privileges.',
        };
    }

    /**
     * Abilities granted to the role by `php artisan abilities:refresh`.
     */
    public function defaultPermissions(): array
    {
        return match ($this) {
            self::SuperAdmin => UserAbility::forRole($this),
            // Staff run the shop day to day; settings, user management and email templates stay with super admins.
            self::Staff => [
                UserAbility::ReadUsers->value,
                UserAbility::ManageCatalog->value,
                UserAbility::ManageOrders->value,
                UserAbility::ManageCoupons->value,
                UserAbility::ManageReviews->value,
                UserAbility::ManageContent->value,
                UserAbility::ViewReports->value,
            ],
            self::User => [],
        };
    }
}
