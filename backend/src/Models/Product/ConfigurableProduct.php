<?php

namespace App\Models\Product;

class ConfigurableProduct extends AbstractProduct
{
    public function getType(): string
    {
        return 'configurable';
    }
}