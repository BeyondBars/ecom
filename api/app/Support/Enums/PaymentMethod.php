<?php

namespace App\Support\Enums;

enum PaymentMethod: string
{
    case Razorpay = 'razorpay';
    case CashOnDelivery = 'cod';
}
