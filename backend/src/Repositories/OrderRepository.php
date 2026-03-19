<?php

namespace App\Repositories;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Config\Database;
use App\Models\Order;
use App\Repositories\ProductRepository;

class OrderRepository
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function create(array $items, float $total): Order
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("INSERT INTO orders (total) VALUES (:total)");
            $stmt->execute(['total' => $total]);
            $orderId = (int)$db->lastInsertId();

            $stmtItem = $db->prepare("
                INSERT INTO order_items (order_id, product_id, quantity)
                VALUES (:order_id, :product_id, :quantity)
            ");

            $finalItems = [];

            foreach ($items as $item) {
            $productId = (string)$item['productId'];
            $product = $this->productRepository->findById($productId);

            if (!$product) throw new \RuntimeException("Product NOT FOUND: " . $productId);

            $stmtItem->execute([
                'order_id' => $orderId,
                'product_id' => $product->getId(),
                'quantity' => $item['quantity']
            ]);

            $orderItemId = (int)$db->lastInsertId();

            $itemAttributes = $item['attributes'] ?? [];
            foreach ($itemAttributes as $attr) {
                if (!isset($attr['name']) || !isset($attr['value'])) continue;

                $stmtAttr = $db->prepare("
                    INSERT INTO order_item_attributes (order_item_id, attribute_name, attribute_value)
                    VALUES (:order_item_id, :attribute_name, :attribute_value)
                ");
                $stmtAttr->execute([
                    'order_item_id' => $orderItemId,
                    'attribute_name' => $attr['name'],
                    'attribute_value' => $attr['value']
                ]);
            }

            $formattedAttributes = [];
            if (is_array($itemAttributes)) {
                foreach ($itemAttributes as $attr) {
                    if (!is_array($attr)) continue;
                    if (!isset($attr['name']) || !isset($attr['value'])) continue;

                    $formattedAttributes[] = [
                        'name' => $attr['name'],
                        'type' => null,
                        'items' => [['value' => $attr['value'], 'displayValue' => $attr['value']]]
                    ];
                }
            }

            $finalItems[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'attributes' => $formattedAttributes
            ];
        }

            $db->commit();

            return new Order([
                'id' => $orderId,
                'items' => $finalItems,
                'total' => $total
            ]);

        } catch (\Exception $e) {
            $db->rollBack();

            error_log("ORDER ERROR: " . $e->getMessage());
            error_log($e->getTraceAsString());

            throw new \RuntimeException($e->getMessage());
        }
    }
}