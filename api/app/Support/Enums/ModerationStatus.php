<?php

namespace App\Support\Enums;

/**
 * Reviews and blog comments are shown publicly only once approved.
 */
enum ModerationStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
