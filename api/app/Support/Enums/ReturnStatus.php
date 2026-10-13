<?php

namespace App\Support\Enums;

enum ReturnStatus: string
{
    case Requested = 'requested';
    case Approved = 'approved';
    case Rejected = 'rejected';
    // Items received back and the refund issued
    case Completed = 'completed';
}
