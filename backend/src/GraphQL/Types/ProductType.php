<?php
declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Product\AbstractProduct;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType extends ObjectType
{
    public function __construct()
    {
        $attributeItemType = new ObjectType([
            'name' => 'AttributeItem',
            'fields' => [
                'value' => Type::string(),
                'displayValue' => Type::string(),
            ],
        ]);

        $attributeType = new ObjectType([
            'name' => 'Attribute',
            'fields' => [
                'name' => Type::nonNull(Type::string()),
                'type' => Type::nonNull(Type::string()),
                'items' => Type::listOf($attributeItemType),
            ],
        ]);

        parent::__construct([
            'name' => 'Product',
            'fields' => [

                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'resolve' => fn($product) =>
                        $product instanceof AbstractProduct ? $product->getId() : null,
                ],

                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn($product) =>
                        $product instanceof AbstractProduct ? $product->getName() : null,
                ],

                'brand' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) =>
                        $product instanceof AbstractProduct ? $product->getBrand() : null,
                ],

                'description' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) =>
                        $product instanceof AbstractProduct ? $product->getDescription() : '',
                ],

                'price' => [
                    'type' => Type::nonNull(Type::float()),
                    'resolve' => fn($product) =>
                        $product instanceof AbstractProduct ? $product->getPrice() : 0,
                ],

                'gallery' => [
                    'type' => Type::listOf(Type::string()),
                    'resolve' => fn($product) =>
                        $product instanceof AbstractProduct ? $product->getGallery() : [],
                ],

                'inStock' => [
                    'type' => Type::boolean(),
                    'resolve' => fn($product) =>
                        $product instanceof AbstractProduct ? $product->isInStock() : false,
                ],

                'attributes' => [
                    'type' => Type::listOf($attributeType),
                    'resolve' => function(AbstractProduct $product) {
                        $attrs = $product->getAttributes() ?? [];
                        return array_map(function($attr) {
                            return [
                                'name' => method_exists($attr, 'getName') ? $attr->getName() ?? 'unknown' : 'unknown',
                                'type' => method_exists($attr, 'getType') ? $attr->getType() ?? 'text' : 'text',
                                'items' => method_exists($attr, 'getItems') ? $attr->getItems() ?? [] : [],
                            ];
                        }, $attrs);
                    },
                ],
            ]
        ]);
    }
}