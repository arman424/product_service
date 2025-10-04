<?php
declare(strict_types=1);

namespace App\Action;

use App\Entity\Product;
use Shared\Bundle\DTO\ProductDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ProductListAction
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(): JsonResponse
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        $data = array_map(fn($p) => ProductDTO::fromEntity($p), $products);

        return new JsonResponse(['data' => $data]);
    }
}