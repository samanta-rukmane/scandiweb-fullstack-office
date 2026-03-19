<?php

namespace App\Models;

// Represents a customer order
class Order
{
    protected int $id;
    protected array $items;
    protected float $total;

    /**
     * Order constructor
     *
     * @param array $data Array containing 'id', 'items', and 'total'
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->items = $data['items'] ?? [];
        $this->total = $data['total'] ?? 0.0;
    }

    // Get order ID, items, total
    public function getId(): int { return $this->id; }
    public function getItems(): array { return $this->items; }
    public function getTotal(): float { return $this->total; }
}