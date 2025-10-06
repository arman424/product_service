<?php
declare(strict_types=1);

namespace App\Controller;

use App\Action\ProductCreateAction;
use App\Action\ProductGetAction;
use App\Action\ProductListAction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductCreateAction $productCreateAction,
        private readonly ProductListAction $productListAction,
        private readonly ProductGetAction $productGetAction
    ) {}

    #[Route('/products', methods: ['POST'])]
    public function create(Request $request, ): JsonResponse
    {
        return ($this->productCreateAction)($request->getContent());
    }

    #[Route('/products', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return ($this->productListAction)();
    }

    #[Route('/products/{id}', methods: ['GET'])]
    public function getProduct(string $id): JsonResponse
    {
        return ($this->productGetAction)($id);
    }
}
