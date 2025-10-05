<?php
declare(strict_types=1);

namespace App\Action;

use App\Request\ProductCreateRequest;
use App\Entity\Product;
use App\Service\ProductPublisher;
use Doctrine\ORM\EntityManagerInterface;
use Shared\Bundle\DTO\ProductDTO;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductCreateAction
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private ProductPublisher $publisher
    ) {}

    /**
     * @param $productData
     * @return Product
     */
    public function __invoke($productData): Product
    {
        $productData = json_decode($productData, true);
        $request = $this->mapRequest($productData);

        $errors = $this->validator->validate($request);
        if (count($errors) > 0) {
            throw new ValidationFailedException($request, $errors);
        }

        $product = new Product();
        $product->setId(Uuid::v4());
        $product->setName($request->name);
        //Should be integer in cents to avoid float precision issues
        //TODO MoneyObject library can be used here
        $product->setPrice((int) $request->price);
        $product->setQuantity($request->quantity);

        $this->em->persist($product);
        $this->em->flush();

        $dto = ProductDTO::fromEntity($product);
        $this->publisher->publish($dto);

        return $product;
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
