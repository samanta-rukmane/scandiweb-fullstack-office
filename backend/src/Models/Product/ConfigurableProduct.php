<?php

namespace App\Models\Product;

class ConfigurableProduct extends AbstractProduct
{
    public function getType(): string
    {
        return 'configurable';
    }

    public function getAttributes(): array
    {
        return $this->attributes ?? [];
    }
}