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
                $product = $this->productRepository->findById((string)$item['productId']);
                if (!$product) {
                    throw new \RuntimeException("Product with ID {$item['productId']} not found");
                }

                $stmtItem->execute([
                    'order_id' => $orderId,
                    'product_id' => $product->getId(),
                    'quantity' => $item['quantity'],
                    'selected_attributes' => json_encode($item['attributes'])
                ]);

                $formattedAttributes = [];
                foreach ($product->getAttributes() as $attr) {
                    $attrValues = array_map(fn($i) => $i['value'], $attr->getItems());
                    $selectedValues = array_filter($item['attributes'], fn($val) => in_array($val, $attrValues, true));

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
            file_put_contents(
                __DIR__ . '/../../order_error.log', 
                date('Y-m-d H:i:s') . " - " . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL, 
                FILE_APPEND
            );
            throw $e;
        }
    }
}