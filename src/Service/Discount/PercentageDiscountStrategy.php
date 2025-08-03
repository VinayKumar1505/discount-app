<?php

namespace App\Service\Discount;

class PercentageDiscountStrategy extends AbstractDiscountStrategy
{
    /**
     * @var float
     */
    private $discountPercentage;

    public function __construct(float $discountPercentage)
    {
        parent::__construct("{$discountPercentage}% off");
        $this->setDiscountPercentage($discountPercentage);
    }

    public function setDiscountPercentage(float $discountPercentage): void
    {
        if ($discountPercentage < 0 || $discountPercentage > 100) {
            throw new \InvalidArgumentException('Discount percentage must be between 0 and 100.');
        }
        $this->discountPercentage = $discountPercentage;
    }

    public function applyDiscount(float $originalPrice): float
    {
        $this->validatePrice($originalPrice);
        $discountedPrice = $originalPrice * ($this->discountPercentage / 100);
        $finalPrice= $originalPrice - $discountedPrice;
        return round($finalPrice, 2);
    }
}