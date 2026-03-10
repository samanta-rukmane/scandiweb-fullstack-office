<?php
namespace App\Repositories;

use App\Config\Database;
use App\Models\Category;

class CategoryRepository
{
    public function findAll(): array
    {
        $db = Database::getConnection();
        $rows = $db->query("SELECT * FROM categories")
                   ->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => new Category($row), $rows);
    }

    public function findBySlug(string $slug): ?Category
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? new Category($row) : null;
    }
}