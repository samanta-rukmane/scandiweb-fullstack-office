<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Attribute',
            'description' => 'Represents a product attribute, e.g. color, size, etc.',
            'fields' => [
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The name of the attribute',
                ],
                'type' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The type of attribute (text, swatch, etc.)',
                ],
                'items' => [
                    'type' => Type::listOf(new AttributeItemType()),
                    'description' => 'List of selectable items for this attribute',
                    'resolve' => fn($attr) => $attr['items'] ?? [],
                ],
            ],
        ];

        parent::__construct($config);
    }
}