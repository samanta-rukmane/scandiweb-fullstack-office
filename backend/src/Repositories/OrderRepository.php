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
                INSERT INTO order_items (order_id, product_id, quantity, selected_attributes)
                VALUES (:order_id, :product_id, :quantity, :selected_attributes)
            ");

            $finalItems = [];

            foreach ($items as $item) {
                $productId = (string)$item['productId'];
                error_log("CHECK PRODUCT ID: " . $productId);

                $product = $this->productRepository->findById($productId);

                if (!$product) {
                    throw new \RuntimeException("Product NOT FOUND: " . $productId);
                }

                $stmtItem->execute([
                    'order_id' => $orderId,
                    'product_id' => $product->getId(),
                    'quantity' => $item['quantity'],
                    'selected_attributes' => json_encode($item['attributes'])
                ]);

                $formattedAttributes = [];
                $productAttributes = $product->getAttributes() ?? [];

                    foreach ($productAttributes ?? [] as $attr) {
                    $items = $attr->getItems() ?? [];

                    $attrValues = array_map(
                        fn($i) => is_array($i) ? $i['value'] : $i->getValue(),
                        $items
                    );
                    $itemAttributes = $item['attributes'] ?? [];

                    $selectedValues = array_filter(
                        $itemAttributes,
                        fn($val) => in_array($val, $attrValues, true)
                    );

                    $attrItems = array_map(fn($val) => [
                        'value' => $val,
                        'displayValue' => $val
                    ], $selectedValues);

                    if (!empty($attrItems)) {
                        $formattedAttributes[] = [
                            'name' => $attr->getName(),
                            'type' => $attr->getTypeName(),
                            'items' => $attrItems
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