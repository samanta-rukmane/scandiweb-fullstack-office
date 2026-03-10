<?php

namespace App\Models\Product;

class SimpleProduct extends AbstractProduct
{
    public function getType(): string
    {
        return 'simple';
    }

    public function getAttributes(): array
    {
        return $this->attributes ?? [];
    }
}