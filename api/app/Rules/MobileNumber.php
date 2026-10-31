<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Indian mobile number: 10 digits starting with 6-9, optionally prefixed with +91, 91 or 0.
 * Australian numbers (04xx xxx xxx or +61 4xx) are still accepted for existing accounts.
 */
class MobileNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $number = preg_replace('/[\s\-()]/', '', (string) $value);

        $isIndian = (bool) preg_match('/^(\+?91|0)?[6-9]\d{9}$/', $number);

        if (str_starts_with($number, '+61')) {
            $number = '0'.substr($number, 3);
        }

        if (!$isIndian && !preg_match('/^04\d{8}$/', $number)) {
            $fail('validation.mobile_number')->translate();
        }
    }
}
