<?php
namespace App\Models;

class Order
{
    protected int $id;
    protected array $items;
    protected float $total;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->items = $data['items'] ?? [];
        $this->total = $data['total'] ?? 0.0;
    }

    public function getId(): int { return $this->id; }
    public function getItems(): array { return $this->items; }
    public function getTotal(): float { return $this->total; }
}