<?php

namespace App\Support\Enums;

enum BlogStatus: string
{
    case Enabled = 'enabled';
    case Pending = 'pending';
    case Disabled = 'disabled';

    public function label(): string
    {
        return match ($this) {
            self::Enabled => 'Enabled',
            self::Pending => 'Pending',
            self::Disabled => 'Disabled',
        };
    }

    public function class(): string
    {
        return match ($this) {
            self::Enabled => 'status-success',
            self::Pending => 'status-info',
            self::Disabled => 'status-danger',
        };
    }
}
