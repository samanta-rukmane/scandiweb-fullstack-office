<?php
namespace App\Models\Product;

abstract class AbstractProduct
{
    protected string $id;
    protected string $name;
    protected string $brand;
    protected float $price;
    protected array $gallery;
    protected bool $inStock;
    protected ?string $categoryId = null;
    protected ?string $description = null;

    protected array $attributes = [];

    public function __construct(array $data)
    {
        $this->id = (string)$data['id'];
        $this->name = $data['name'] ?? '';
        $this->brand = $data['brand'] ?? '';
        $this->price = (float)($data['price'] ?? 0);
        $this->gallery = $data['gallery'] ?? [];
        $this->inStock = (bool)($data['inStock'] ?? false);
        $this->categoryId = $data['categoryId'] ?? null;
        $this->attributes = $data['attributes'] ?? [];
        $this->description = $data['description'] ?? '';
    }

    abstract public function getType(): string;

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getBrand(): string { return $this->brand; }
    public function getPrice(): float { return $this->price; }
    public function getGallery(): array { return $this->gallery; }
    public function isInStock(): bool { return $this->inStock; }
    public function getAttributes(): array { return $this->attributes; }
    public function getCategoryId(): ?string { return $this->categoryId; }
    public function getDescription(): ?string { return $this->description ?? ''; }
}