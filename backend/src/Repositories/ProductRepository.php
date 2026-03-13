<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use App\Models\Product\AbstractProduct;
use App\Models\Product\ProductFactory;

class ProductRepository
{
    private AttributeRepository $attributeRepo;

    public function __construct()
    {
        $this->attributeRepo = new AttributeRepository();
    }


    /** @return AbstractProduct[] */
    public function findAll(): array
    {
        $db = Database::getConnection();
        $rows = $db->query("SELECT * FROM products")->fetchAll(\PDO::FETCH_ASSOC);

        return array_map([$this, 'mapToProduct'], $rows);
    }


    public function findById(string $id): ?AbstractProduct
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->mapToProduct($row);
    }


    /** @return AbstractProduct[] */
    public function findByCategory(string $categoryName): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM categories WHERE name = :name LIMIT 1");
        $stmt->execute(['name' => strtolower($categoryName)]);
        $category = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$category) {
            return [];
        }

        $stmt = $db->prepare("SELECT * FROM products WHERE category_id = :cid");
        $stmt->execute(['cid' => $category['id']]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map([$this, 'mapToProduct'], $rows);
    }


    private function mapToProduct(array $row): AbstractProduct
    {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT amount FROM prices WHERE product_id = :pid LIMIT 1");
        $stmt->execute(['pid' => $row['id']]);
        $priceRow = $stmt->fetch(\PDO::FETCH_ASSOC);
        $price = $priceRow ? (float)$priceRow['amount'] : 0.0;

        $stmt = $db->prepare("SELECT image_url FROM product_galleries WHERE product_id = :pid");
        $stmt->execute(['pid' => $row['id']]);
        $galleryRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $gallery = array_map(fn($r) => $r['image_url'] ?? '', $galleryRows);

        $rawAttributes = $this->attributeRepo->findByProductId((string)$row['id']) ?? [];
        $attributes = array_filter($rawAttributes, fn($attr) => $attr instanceof \App\Models\Attribute\AbstractAttribute);

        $data = [
            'id' => (string)$row['id'],
            'name' => $row['name'],
            'brand' => $row['brand'],
            'price' => $price,
            'inStock' => (bool)$row['in_stock'],
            'gallery' => $gallery,
            'attributes' => $attributes,
            'categoryId' => (string)$row['category_id'],
            'description' => $row['description'] ?? '',
            'type' => $row['type'] ?? 'simple',
        ];

        return ProductFactory::create($data);
    }
}