<?php

namespace App\Service;

use Shared\Bundle\DTO\PublishedDTOInterface;
use Shared\Bundle\Messaging\ProductOutOfStockMessage;
use Shared\Bundle\Publisher\Publisher;

class ProductOutOfStockPublisher extends Publisher
{
    public function publish(PublishedDTOInterface $publishedDTO): void
    {
        $this->messageBus->dispatch(new ProductOutOfStockMessage($publishedDTO));
    }
}