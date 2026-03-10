<?php

namespace App\Models\Attribute;

abstract class AbstractAttribute
{
    protected string $id;
    protected string $name;
    protected string $type;
    protected array $items;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->type = $this->getType();
        $this->items = $data['items'] ?? [];
    }

    abstract public function getType(): string;

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getTypeName(): string { return $this->type; }
    public function getItems(): array { return $this->items; }

    public function toArray(): array
    {
        $items = array_map(fn($item) => [
            'value' => $item['value'] ?? null,
            'displayValue' => $item['display_value'] ?? $item['value'] ?? null,
        ], $this->items);

        return [
            'name' => $this->name,
            'type' => $this->type,
            'items' => $items,
        ];
    }
}