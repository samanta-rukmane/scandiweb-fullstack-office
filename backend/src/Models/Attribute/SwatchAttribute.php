<?php

namespace App\Models\Attribute;

class SwatchAttribute extends AbstractAttribute
{
    public function getType(): string
    {
        return 'swatch';
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'items' => $this->items,
        ];
    }
}