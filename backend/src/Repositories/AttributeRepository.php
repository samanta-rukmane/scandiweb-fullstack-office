<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use App\Models\Attribute\TextAttribute;
use App\Models\Attribute\SwatchAttribute;

class AttributeRepository
{
    public function findByProductId(string $productId): array
    {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM attributes WHERE product_id = :pid");
        $stmt->execute(['pid' => $productId]);
        $attributes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];
        foreach ($attributes as $attr) {
            $stmtItems = $db->prepare("SELECT value, display_value FROM attribute_items WHERE attribute_id = :aid");
            $stmtItems->execute(['aid' => $attr['id']]);
            $attr['items'] = $stmtItems->fetchAll(\PDO::FETCH_ASSOC);

            $mapped = $this->mapToAttribute($attr);

            if ($mapped instanceof \App\Models\Attribute\AbstractAttribute) {
                $result[] = $mapped;
            }
        }

        return $result;
    }

    private function mapToAttribute(array $row)
    {
        $row['items'] = $row['items'] ?? [];

        try {
            switch ($row['type']) {
                case 'text':
                    return new TextAttribute($row);
                case 'swatch':
                    return new SwatchAttribute($row);
                default:
                    return null;
            }
        } catch (\Throwable $e) {
            error_log('Failed to map attribute: ' . $e->getMessage());
            return null;
        }
    }
}