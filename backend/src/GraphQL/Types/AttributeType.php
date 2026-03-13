<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Models\Attribute\AbstractAttribute;

class AttributeType extends ObjectType
{
    public function __construct()
    {
        $attributeItemType = new AttributeItemType();

        parent::__construct([
            'name' => 'Attribute',
            'fields' => [
                'name' => Type::nonNull(Type::string()),
                'type' => Type::nonNull(Type::string()),
                'items' => [
                    'type' => Type::listOf($attributeItemType),
                    'resolve' => fn(AbstractAttribute $attr) => $attr->getItems(),
                ],
            ],
        ]);
    }
}