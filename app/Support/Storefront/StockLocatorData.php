<?php

namespace App\Support\Storefront;

class StockLocatorData
{
    public static function rows(): array
    {
        return config('storefront.stock_locator_demo', []);
    }

    public static function categories(): array
    {
        return array_values(array_unique(array_filter(array_map(
            fn (array $row) => $row['category'] ?? null,
            static::rows(),
        ))));
    }

    public static function locations(): array
    {
        return array_values(array_unique(array_filter(array_map(
            fn (array $row) => $row['location'] ?? null,
            static::rows(),
        ))));
    }

    public static function statuses(): array
    {
        return array_values(array_unique(array_filter(array_map(
            fn (array $row) => $row['stock_status'] ?? null,
            static::rows(),
        ))));
    }

    public static function summary(): array
    {
        $rows = static::rows();

        return [
            'rows' => count($rows),
            'locations' => count(static::locations()),
            'categories' => count(static::categories()),
            'inStock' => count(array_filter($rows, fn (array $row) => ($row['stock_status'] ?? null) === 'In Stock')),
        ];
    }
}
