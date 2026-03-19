<?php

namespace App\Models\Attribute;

// Represents a text-type product attribute (e.g., size, capacity)
class TextAttribute extends AbstractAttribute
{
    // Return the type of the attribute
    public function getType(): string { return 'text'; }

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