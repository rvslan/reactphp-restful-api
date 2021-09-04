<?php

namespace App\Orders\Controller\Output;

use App\Orders\Order as OrderEntity;

final class Order
{
    public $id;
    public $productId;
    public $quantity;
    public $request;


    public function __construct(int $id, int $productId, int $quantity, Request $request)
    {
        $this->id = $id;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->request = $request;
    }

    public static function fromEntity(OrderEntity $entity, Request $request): self
    {
        return new self($entity->id, $entity->productId, $entity->quantity, $request);
    }
}
