<?php

namespace App\Models\Attribute;

class TextAttribute extends AbstractAttribute
{
    public function getType(): string
    {
        return 'text';
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