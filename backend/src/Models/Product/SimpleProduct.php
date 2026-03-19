<?php

namespace App\Models\Product;

// Represents a simple product without configurable attributes
class SimpleProduct extends AbstractProduct
{
    // Return the type of the product
    public function getType(): string { return 'simple'; }
}