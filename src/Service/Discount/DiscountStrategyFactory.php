<?php

namespace App\Service\Discount;


class DiscountStrategyFactory
{
    private array $strategyMap;

    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $strategy) {
            $label = (new \ReflectionClass($strategy))->getShortName();
            $key = strtolower(str_replace('DiscountStrategy', '', $label));
            $this->strategyMap[$key] = $strategy;
        }
    }

    public function create(string $type, float $value = 0, int $quantity = 1): DiscountStrategyInterface
    {
        return match ($type) {
            'percentage' => new PercentageDiscountStrategy($value),
            'fixed' => new FixedAmountDiscountStrategy($value),
            'bogo' => new BOGODiscountStrategy($quantity),
            default => throw new \InvalidArgumentException("Unknown discount type: $type"),
        };
    }
}
