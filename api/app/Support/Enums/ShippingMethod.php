<?php

namespace App\Support\Enums;

enum ShippingMethod: string
{
    case Standard = 'standard';
    case Express = 'express';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Standard Shipping (3-5 business days)',
            self::Express => 'Express Shipping (1-2 business days)',
        };
    }
}
