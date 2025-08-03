<?php
namespace App\Service\Discount;

abstract class AbstractDiscountStrategy implements DiscountStrategyInterface
{
    /**
     * @var string
     */
    protected $label;

    public function __construct(string $label= 'Generic Discount')
    {
        $this->label = $label;
    }

    /**
     * Get the label for the discount strategy.
     *
     * @return string The label describing the discount strategy.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    public function validatePrice(float $originalPrice): void
    {
        if ($originalPrice < 0) {
            throw new \InvalidArgumentException('Original price cannot be negative.');
        }
    }

    abstract public function applyDiscount(float $originalPrice): float;
}