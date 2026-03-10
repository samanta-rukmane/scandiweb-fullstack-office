<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeItemType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'AttributeItem',
            'fields' => [
                'value' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Internal value of the attribute item',
                ],
                'displayValue' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'User-friendly display value',
                    'resolve' => fn($item) => $item['display_value'] ?? $item['displayValue'] ?? null,
                ],
            ],
            'description' => 'Represents a single selectable option of a product attribute',
        ];

        parent::__construct($config);
    }
}