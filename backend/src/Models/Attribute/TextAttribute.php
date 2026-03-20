<?php

declare(strict_types=1);

namespace App\Models\Attribute;

// Text attribute (e.g., size, capacity)
class TextAttribute extends AbstractAttribute
{
    // Returns attribute type identifier
    public function getType(): string { return 'text'; }

    /**
     * Convert attribute to array for GraphQL response
     *
     * Note: parent already normalizes items if needed
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'items' => $this->items,
        ];
    }
}