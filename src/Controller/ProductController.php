<?php
declare(strict_types=1);

namespace App\Controller;

use App\Action\ProductCreateAction;
use App\Action\ProductGetAction;
use App\Action\ProductListAction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
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
        try {
            $product = ($this->productCreateAction)($request->getContent());
        } catch (ValidationFailedException $e) {
            $errors = [];
            foreach ($e->getViolations() as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return $this->json(['errors' => $errors], 422);
        }

        return $this->json($product);
    }

    #[Route('/products', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        return ($this->productListAction)($em);
    }

    #[Route('/products/{id}', methods: ['GET'])]
    public function getProduct(string $id, EntityManagerInterface $em): JsonResponse
    {
        return ($this->productGetAction)($id, $em);
    }
}
