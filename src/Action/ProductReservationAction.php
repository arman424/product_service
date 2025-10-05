<?php

namespace App\Action;

use App\Entity\Product;
use App\Service\ProductOutOfStockPublisher;
use App\Service\ProductReservedPublisher;
use Doctrine\ORM\EntityManagerInterface;
use Shared\Bundle\DTO\OrderReservationDTO;
use Shared\Bundle\DTO\ProductOutOfStockDTO;
use Shared\Bundle\DTO\PublishedDTOInterface;

final class ProductReservationAction
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProductReservedPublisher $productReservedPublisher,
        private ProductOutOfStockPublisher $productOutOfStockPublisher
    ) {}

    public function __invoke(PublishedDTOInterface $event): void
    {
        $product = $this->em->getRepository(Product::class)->find($event->productId);

        if ($product && $product->getQuantity() >= $event->quantity) {
            $product->setQuantity($product->getQuantity() - $event->quantity);
            $this->em->flush();

            $this->productReservedPublisher->publish(
                OrderReservationDTO::init([
                    'orderId' => $event->orderId,
                    'productId' => $event->productId,
                    'quantity' => $event->quantity,
                ]),
            );
        } else {
            $this->productOutOfStockPublisher->publish(
                ProductOutOfStockDTO::init([
                    'orderId' => $event->orderId,
                    'quantity' => $event->quantity,
                ])
            );
        }
    }
}