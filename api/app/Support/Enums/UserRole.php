<?php

namespace App\Support\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case User = 'user';
    case Staff = 'staff';
    case Blogger = 'blogger';
    case Invoicer = 'invoicer';

    public function title(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::User => 'User',
            self::Staff => 'Staff',
            self::Blogger => 'Blogger',
            self::Invoicer => 'Invoicer',
        };
    }

    public function desc(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Has full access to all system features, settings, and user management.',
            self::User => 'Standard user with access to general features and personal profile management.',
            self::Staff => 'Internal team member with access to operational tools and limited administrative privileges.',
            self::Blogger => 'Can write, edit, and publish blog content, and manage their own posts.',
            self::Invoicer => 'Responsible for generating, sending, and managing invoices and payment records.',
        };
    }

    /**
     * Abilities granted to the role by `php artisan abilities:refresh`.
     */
    public function defaultPermissions(): array
    {
        return match ($this) {
            self::SuperAdmin => UserAbility::forRole($this),
            self::Staff => [UserAbility::ReadUsers->value],
            self::User, self::Blogger, self::Invoicer => [],
        };
    }
}
