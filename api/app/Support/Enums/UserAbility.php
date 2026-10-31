<?php

namespace App\Support\Enums;

enum UserAbility: string
{
    case ReadUsers = 'read users';
    case WriteUsers = 'write users';
    case ManageEmailTemplates = 'manage email templates';
    case ManageCatalog = 'manage catalog';
    case ManageOrders = 'manage orders';
    case ManageCoupons = 'manage coupons';
    case ManageReviews = 'manage reviews';
    case ManageContent = 'manage content';
    case ManageSettings = 'manage settings';
    case ViewReports = 'view reports';

    /**
     * Abilities grouped for display/syncing. "excludes" lists role values that may never hold it.
     */
    public static function grouped(): array
    {
        $adminOnly = [UserRole::User->value];

        return [
            'users' => [
                'read' => ['name' => self::ReadUsers, 'excludes' => []],
                'write' => ['name' => self::WriteUsers, 'excludes' => []],
            ],
            'email templates' => [
                'manage' => ['name' => self::ManageEmailTemplates, 'excludes' => []],
            ],
            'catalog' => [
                'manage' => ['name' => self::ManageCatalog, 'excludes' => $adminOnly],
            ],
            'orders' => [
                'manage' => ['name' => self::ManageOrders, 'excludes' => $adminOnly],
            ],
            'coupons' => [
                'manage' => ['name' => self::ManageCoupons, 'excludes' => $adminOnly],
            ],
            'reviews' => [
                'manage' => ['name' => self::ManageReviews, 'excludes' => $adminOnly],
            ],
            'content' => [
                'manage' => ['name' => self::ManageContent, 'excludes' => $adminOnly],
            ],
            'settings' => [
                'manage' => ['name' => self::ManageSettings, 'excludes' => $adminOnly],
            ],
            'reports' => [
                'view' => ['name' => self::ViewReports, 'excludes' => $adminOnly],
            ],
        ];
    }

    /**
     * All ability values, minus any excluded for the given role.
     */
    public static function forRole(UserRole $role): array
    {
        $abilities = [];

        foreach (static::grouped() as $group) {
            foreach ($group as $ability) {
                if (!in_array($role->value, $ability['excludes'], true)) {
                    $abilities[] = $ability['name']->value;
                }
            }
        }

        return $abilities;
    }
}
