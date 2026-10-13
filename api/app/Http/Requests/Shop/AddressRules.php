<?php

namespace App\Http\Requests\Shop;

/**
 * Indian delivery address rules, shared by the address book and checkout.
 */
class AddressRules
{
    public static function rules(string $prefix = '', bool $required = true): array
    {
        $req = $required ? 'required' : 'sometimes';

        return [
            "{$prefix}label" => ['sometimes', 'nullable', 'string', 'max:50'],
            "{$prefix}first_name" => [$req, 'string', 'max:100'],
            "{$prefix}last_name" => [$req, 'string', 'max:100'],
            "{$prefix}phone" => [$req, 'string', 'regex:/^(\+91[\s-]?)?[6-9]\d{9}$/'],
            "{$prefix}line1" => [$req, 'string', 'max:255'],
            "{$prefix}line2" => ['nullable', 'string', 'max:255'],
            "{$prefix}city" => [$req, 'string', 'max:100'],
            "{$prefix}state" => [$req, 'string', 'max:100'],
            "{$prefix}postal_code" => [$req, 'string', 'regex:/^[1-9]\d{5}$/'],
            "{$prefix}country" => ['sometimes', 'string', 'in:India'],
        ];
    }

    public static function messages(string $prefix = ''): array
    {
        return [
            "{$prefix}phone.regex" => 'Enter a valid 10-digit mobile number.',
            "{$prefix}postal_code.regex" => 'Enter a 6-digit PIN code.',
            "{$prefix}country.in" => 'We currently deliver within India only.',
        ];
    }
}
