<?php

namespace App\Support\Enums;

enum CouponType: string
{
    case Percent = 'percent';
    case Fixed = 'fixed';
    case FreeShipping = 'free_shipping';
}
