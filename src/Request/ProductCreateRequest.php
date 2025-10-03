<?php
declare(strict_types=1);

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductCreateRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public $name;

    #[Assert\NotNull]
    #[Assert\Type('float')]
    #[Assert\GreaterThanOrEqual(0)]
    public $price;

    #[Assert\NotNull]
    #[Assert\Type('integer')]
    #[Assert\GreaterThanOrEqual(0)]
    public $quantity;
}

