<?php
declare(strict_types=1);

namespace App\Action;

use App\Entity\Product;
use Shared\Bundle\DTO\ProductDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ProductGetAction
{
    public function __invoke(string $id, EntityManagerInterface $em): JsonResponse
    {
        $product = $em->getRepository(Product::class)->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        return new JsonResponse(ProductDTO::fromEntity($product));
    }
}

