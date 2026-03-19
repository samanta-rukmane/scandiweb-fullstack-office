<?php

namespace App\Models\Attribute;

// Represents a swatch-type product attribute (e.g., color)
class SwatchAttribute extends AbstractAttribute
{
    // Return the type of the attribute
    public function getType(): string { return 'swatch'; }

    // Convert attribute to array for GraphQL response
    public function toArray(): array
    {
        return [
            'name'  => $this->name,
            'type'  => $this->type,
            'items' => $this->items,
        ];
    }
}