<?php

namespace App\Service\Discount;

class FixedAmountDiscountStrategy extends AbstractDiscountStrategy
{
    /**
     * @var float
     */
    private $discountAmount;

    public function __construct(float $discountAmount)
    {
        parent::__construct("Flat ₹{$discountAmount} off");
        $this->setDiscountAmount($discountAmount);
    }

    public function setDiscountAmount(float $discountAmount): void
    {
        if ($discountAmount < 0) {
            throw new \InvalidArgumentException('Discount amount cannot be negative.');
        }
        $this->discountAmount = $discountAmount;
    }

    public function applyDiscount(float $originalPrice): float
    {
        $this->validatePrice($originalPrice);
        $finalPrice = $originalPrice - $this->discountAmount;
        return max(0, round($finalPrice, 2));
    }
}