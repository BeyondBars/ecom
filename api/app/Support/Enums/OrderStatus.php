<?php

namespace App\Support\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Returned = 'returned';

    /**
     * Statuses an admin may move an order to from this one. Returns go through the return flow.
     */
    public function nextStatuses(): array
    {
        return match ($this) {
            self::Pending => [self::Processing, self::Cancelled],
            self::Processing => [self::Shipped, self::Cancelled],
            self::Shipped => [self::Delivered],
            self::Delivered, self::Cancelled, self::Returned => [],
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, $this->nextStatuses(), true);
    }

    /**
     * Customers can cancel until the order ships.
     */
    public function isCancellableByCustomer(): bool
    {
        return in_array($this, [self::Pending, self::Processing], true);
    }
}
