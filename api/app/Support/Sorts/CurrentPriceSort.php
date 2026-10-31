<?php

namespace App\Support\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

/**
 * Sorts by the price customers actually pay (sale price when it's lower than the regular price).
 */
class CurrentPriceSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): void
    {
        $query->orderByRaw(
            'CASE WHEN products.sale_price IS NOT NULL AND products.sale_price < products.price THEN products.sale_price ELSE products.price END '
            .($descending ? 'DESC' : 'ASC')
        );
    }
}
