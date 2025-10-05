<?php

namespace App\Service;

use Shared\Bundle\DTO\PublishedDTOInterface;
use Shared\Bundle\Messaging\ProductReservedMessage;
use Shared\Bundle\Publisher\Publisher;

class ProductReservedPublisher extends Publisher
{
    public function publish(PublishedDTOInterface $publishedDTO): void
    {
        $this->messageBus->dispatch(new ProductReservedMessage($publishedDTO));
    }
}