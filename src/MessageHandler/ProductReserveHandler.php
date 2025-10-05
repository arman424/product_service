<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Action\ProductReservationAction;
use Shared\Bundle\Messaging\OrderMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProductReserveHandler
{
    public function __construct(
        private ProductReservationAction $productReservationAction,
    ) {}

    public function __invoke(OrderMessage $message): void
    {
        $event = $message->orderReservationDTO;
        ($this->productReservationAction)($event);
    }
}
