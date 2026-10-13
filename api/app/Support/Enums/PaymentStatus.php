<?php

namespace App\Support\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    // Paid order was cancelled/returned and the money still has to go back
    case RefundPending = 'refund_pending';
    case Refunded = 'refunded';
}
