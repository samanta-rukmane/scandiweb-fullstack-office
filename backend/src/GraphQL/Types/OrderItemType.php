<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderItemType extends ObjectType
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
            'name' => 'OrderItem',
            'fields' => [
                'product' => new ProductType(),
                'quantity' => Type::nonNull(Type::int()),
                'attributes' => Type::listOf($attributeType),
            ],
        ]);
    }
}