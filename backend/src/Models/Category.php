<?php

namespace App\Models;

// Represents a product category
class Category
{
    protected int $id;
    protected string $name;
    protected string $slug;

    /**
     * Category constructor
     *
     * @param array $data Array containing 'id', 'name', and optional 'slug'
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->slug = $data['slug'] ?? strtolower(str_replace(' ', '-', $this->name));
    }

    // Get category ID, name, slug (URL-friendly)
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getSlug(): string { return $this->slug; }
}