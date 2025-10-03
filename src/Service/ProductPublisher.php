<?php
declare(strict_types=1);

namespace App\Service;

use Shared\Bundle\DTO\ProductDTO;
use Shared\Bundle\RabbitMQ\PublisherInterface;

class ProductPublisher
{
    public function __construct(private readonly PublisherInterface $publisher) {}

    public function publish(ProductDTO $dto): void
    {
        $this->publisher->publish('product_queue', $dto->toArray());
    }
}