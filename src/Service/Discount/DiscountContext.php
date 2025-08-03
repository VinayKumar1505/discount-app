<?php

namespace App\Service\Discount;

class DiscountContext
{
    private DiscountStrategyInterface $strategy;

    // public function __construct(DiscountStrategyInterface $strategy)
    // {
    //     $this->strategy = $strategy;
    // }

    public function setStrategy(DiscountStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function applyDiscount(float $originalPrice): float
    {
        if(!isset($this->strategy)) {
            throw new \LogicException('No discount strategy set.');
        }
        return $this->strategy->applyDiscount($originalPrice);
    }

    public function getLabel(): string
    {
        return $this->strategy->getLabel();
    }
}