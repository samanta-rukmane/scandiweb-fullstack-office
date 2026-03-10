<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Order',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'items' => Type::listOf(new \App\GraphQL\Types\OrderItemType()),
                'total' => Type::nonNull(Type::float()),
            ],
        ];

        parent::__construct($config);
    }
}