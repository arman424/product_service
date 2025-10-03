<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shared\Bundle\Entity\MappedSuperclass\Product as BaseProduct;

#[ORM\Entity]
#[ORM\Table(name: "products")]
class Product extends BaseProduct {}


