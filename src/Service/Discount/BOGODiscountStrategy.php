<?php

namespace App\Service\Discount;

class BOGODiscountStrategy extends AbstractDiscountStrategy
{
    private int $quantity;

    public function __construct(int $quantity)
    {
        parent::__construct("Buy One Get One Free");
        $this->quantity = $quantity;
    }

    public function applyDiscount(float $originalPrice): float
    {
        $this->validatePrice($originalPrice);
        if ($this->quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero.');
        }
        $paidItems = ceil($this->quantity / 2);
        $finalPrice = $originalPrice * $paidItems;
        return max(0, round($finalPrice, 2));
    }
}