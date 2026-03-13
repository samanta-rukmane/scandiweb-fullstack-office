<?php

namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use App\GraphQL\Types\CategoryType;
use App\GraphQL\Types\OrderType;
use App\GraphQL\Types\ProductType;
use App\Repositories\CategoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use RuntimeException;
use Throwable;

class GraphQL
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private OrderRepository $orderRepository;

    private ObjectType $productType;
    private ObjectType $categoryType;
    private ObjectType $orderType;
    private InputObjectType $orderItemInputType;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        OrderRepository $orderRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return string
     */
    public function handle(): string
    {
        try {
            $this->initTypes();
            $schema = $this->buildSchema();
            $input = $this->getInput();

            if (empty($input['query'])) {
                throw new RuntimeException('No query provided');
            }

            $result = GraphQLBase::executeQuery(
                $schema,
                $input['query'],
                null,
                null,
                $input['variables'] ?? null
            );

            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'errors' => [
                    ['message' => $e->getMessage()]
                ]
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');

        return json_encode($output);
    }

    private function buildSchema(): Schema
    {
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'categories' => [
                    'type' => Type::listOf($this->categoryType),
                    'resolve' => fn() => $this->categoryRepository->findAll(),
                ],
                'products' => [
                    'type' => Type::listOf($this->productType),
                    'resolve' => fn() => $this->productRepository->findAll(),
                ],
                'productsByCategory' => [
                    'type' => Type::listOf($this->productType),
                    'args' => [
                        'category' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => fn($root, $args) =>
                        $this->productRepository->findByCategory($args['category']),
                ],
                'product' => [
                    'type' => $this->productType,
                    'args' => [
                        'id' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => fn($root, $args) =>
                        $this->productRepository->findById($args['id']),
                ],
            ],
        ]);

        $orderItemInputType = new InputObjectType([
            'name' => 'OrderItemInput',
            'fields' => [
                'productId' => Type::nonNull(Type::string()),
                'quantity' => Type::nonNull(Type::int()),
                'attributes' => Type::listOf(Type::string()),
            ],
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'placeOrder' => [
                    'type' => $this->orderType,
                    'args' => [
                        'items' => Type::nonNull(Type::listOf(Type::nonNull($this->orderItemInputType))),
                        'total' => Type::nonNull(Type::float()),
                    ],
                    'resolve' => fn($root, $args) =>
                        $this->orderRepository->create($args['items'], $args['total']),
                ],
            ],
        ]);

        return new Schema(
            (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType)
        );
    }

    private function getInput(): array
    {
        $rawInput = file_get_contents('php://input');

        if ($rawInput === false) {
            throw new RuntimeException('Failed to get php://input');
        }

        return json_decode($rawInput, true) ?? [];
    }


    private function initTypes(): void
    {
        $this->productType = new ProductType();
        $this->categoryType = new CategoryType();
        $this->orderType = new OrderType();

        $this->orderItemInputType = new InputObjectType([
            'name' => 'OrderItemInput',
            'fields' => [
                'productId' => Type::nonNull(Type::string()),
                'quantity' => Type::nonNull(Type::int()),
                'attributes' => Type::listOf(Type::string()),
            ],
        ]);
    }
}