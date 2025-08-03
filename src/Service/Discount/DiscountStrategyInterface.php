<?php
namespace App\Service\Discount;

interface DiscountStrategyInterface
{
    /**
     * Apply discount to the original price.
     *
     * @param float $originalPrice The original price before discount.
     * @return float The price after applying the discount.
     */
    public function applyDiscount(float $originalPrice): float;

    /**
     * Get the label for the discount strategy.
     *
     * @return string The label describing the discount strategy.
     */

    public function getLabel(): string;
}