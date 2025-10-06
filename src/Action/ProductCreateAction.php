<?php
declare(strict_types=1);

namespace App\Action;

use App\Request\ProductCreateRequest;
use App\Entity\Product;
use App\Service\ProductPublisher;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Shared\Bundle\DTO\ProductDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductCreateAction
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator,
        private readonly ProductPublisher $publisher
    ) {}

    /**
     * @param string $productData
     * @return JsonResponse
     */
    public function __invoke(string $productData): JsonResponse
    {
        try {
            $productData = json_decode($productData, true);
            $request = $this->mapRequest($productData);

            $errors = $this->validator->validate($request);
            if (count($errors) > 0) {
                throw new ValidationFailedException($request, $errors);
            }

            $product = new Product();
            $product->setId(Uuid::v4());
            $product->setName($request->name);

            // Price stored as integer to avoid floating-point precision issues.
            // TODO: Consider using a dedicated Money object for more robust handling.

            $product->setPrice((int) $request->price);
            $product->setQuantity($request->quantity);

            $this->em->persist($product);
            $this->em->flush();

            $dto = ProductDTO::fromEntity($product);
            $this->publisher->publish($dto);

            return new JsonResponse($product->toArray(), 201);
        } catch (ValidationFailedException $e) {
            $errors = [];
            foreach ($e->getViolations() as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 422);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

    private function mapRequest(array $productData): ProductCreateRequest
    {
        $request = new ProductCreateRequest();
        $request->name = $productData['name'];
        $request->price = $productData['price'];
        $request->quantity = $productData['quantity'];

        return $request;
    }
}
