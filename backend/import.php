<?php

$pdo = new PDO('mysql:host=localhost;dbname=fullstack', 'scandi', 'Password123!');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

$pdo->exec("TRUNCATE TABLE order_item_attributes");
$pdo->exec("TRUNCATE TABLE order_items");
$pdo->exec("TRUNCATE TABLE orders");

$pdo->exec("TRUNCATE TABLE attribute_items");
$pdo->exec("TRUNCATE TABLE attributes");
$pdo->exec("TRUNCATE TABLE prices");
$pdo->exec("TRUNCATE TABLE product_galleries");
$pdo->exec("TRUNCATE TABLE products");
$pdo->exec("TRUNCATE TABLE categories");

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

$json = json_decode(file_get_contents('data.json'), true);
$data = $json['data'];

$categories_inserted = [];
$attributes_inserted = [];
$products_inserted = [];

// --- Categories ---
foreach ($data['categories'] as $category) {
    $name = $category['name'];
    $slug = strtolower(str_replace(' ', '-', $name));

    if (!isset($categories_inserted[$name])) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);
        $categories_inserted[$name] = $pdo->lastInsertId();
    }
}

// --- Products ---
foreach ($data['products'] as $product) {
    if (isset($products_inserted[$product['id']])) continue;

    $category_name = $product['category'] ?? 'all';
    $category_id = $categories_inserted[$category_name] ?? null;

    $in_stock = isset($product['inStock']) ? (int)$product['inStock'] : 1;
    $description = $product['description'] ?? '';
    $brand = $product['brand'] ?? '';

    $stmt = $pdo->prepare("
        INSERT INTO products (id, name, in_stock, description, brand, category_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $product['id'],
        $product['name'],
        $in_stock,
        $description,
        $brand,
        $category_id
    ]);

    $products_inserted[$product['id']] = true;

    foreach ($product['gallery'] ?? [] as $img) {
        $stmt = $pdo->prepare("INSERT INTO product_galleries (product_id, image_url) VALUES (?, ?)");
        $stmt->execute([$product['id'], $img]);
    }

    foreach ($product['prices'] ?? [] as $price) {
        $currency_label = $price['currency']['label'] ?? 'USD';
        $currency_symbol = $price['currency']['symbol'] ?? '$';
        $amount = $price['amount'] ?? 0;

        $stmt = $pdo->prepare("
            INSERT INTO prices (product_id, currency_label, currency_symbol, amount)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$product['id'], $currency_label, $currency_symbol, $amount]);
    }

    foreach ($product['attributes'] ?? [] as $attrSet) {
        $attr_name = $attrSet['name'] ?? null;
        $attr_type = $attrSet['type'] ?? 'text';

        if (!$attr_name) continue;

        if (!isset($attributes_inserted[$product['id']][$attr_name])) {
            $stmt = $pdo->prepare("INSERT INTO attributes (product_id, name, type) VALUES (?, ?, ?)");
            $stmt->execute([$product['id'], $attr_name, $attr_type]);
            $attr_id = $pdo->lastInsertId();
            $attributes_inserted[$product['id']][$attr_name] = $attr_id;
        } else {
            $attr_id = $attributes_inserted[$product['id']][$attr_name];
        }

        foreach ($attrSet['items'] ?? [] as $item) {
            $display_value = $item['displayValue'] ?? '';
            $value = $item['value'] ?? '';

            $stmt = $pdo->prepare("
                INSERT INTO attribute_items (attribute_id, display_value, value)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$attr_id, $display_value, $value]);
        }
    }
}

echo "Import complete\n";