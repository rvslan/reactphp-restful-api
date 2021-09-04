<?php


namespace App\Orders;


final class Order
{
    /**
     * @var int|int
     */
    public $id;
    /**
     * @var int|int
     */
    public $productId;
    /**
     * @var int|int
     */
    public $quantity;

    /**
     * Order constructor.
     * @param int $id
     * @param int $productId
     * @param int $quantity
     */
    public function __construct(int $id, int $productId, int $quantity)
    {
        $this->id = $id;
        $this->productId = $productId;
        $this->quantity = $quantity;
    }
}