<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Category',
            'description' => 'Represents a product category',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'Unique identifier of the category',
                    'resolve' => fn($category) => $category instanceof \App\Models\Category ? $category->getId() : null,
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Category name',
                    'resolve' => fn($category) => $category instanceof \App\Models\Category ? $category->getName() : null,
                ],
                'slug' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Category slug for URLs',
                    'resolve' => fn($category) => $category instanceof \App\Models\Category ? $category->getSlug() : null,
                ],
            ],
        ]);
    }
}