<?php
declare(strict_types=1);

namespace App\Service;

use Shared\Bundle\Messaging\ProductMessage;
use Shared\Bundle\Publisher\Publisher;
use Shared\Bundle\DTO\PublishedDTOInterface;

class ProductPublisher extends Publisher
{
    public function publish(PublishedDTOInterface $publishedDTO): void
    {
        $this->messageBus->dispatch(new ProductMessage($publishedDTO));
    }
}