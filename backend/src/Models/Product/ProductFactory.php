<?php

namespace App\Models\Product;

class ProductFactory
{
    public static function create(array $data): AbstractProduct
    {
        return match($data['type'] ?? 'simple') {
            'simple' => new SimpleProduct($data),
            'configurable' => new ConfigurableProduct($data),
            default => throw new \Exception('Unknown product type')
        };
    }
}