<?php

namespace App\Controller;

use App\Service\Discount\BOGODiscountStrategy;
use App\Service\Discount\DiscountContext;
use App\Service\Discount\DiscountStrategyFactory;
use App\Service\Discount\FixedAmountDiscountStrategy;
use App\Service\Discount\PercentageDiscountStrategy;
use Doctrine\Migrations\Configuration\Migration\Exception\JsonNotValid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // ✅ CORRECT
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscountController extends AbstractController
{
    private DiscountStrategyFactory $factory;
    public function __construct(DiscountStrategyFactory $factory)
    {
        $this->factory = $factory;
    }

    #[Route('/apply-discount', name: 'app_discount_controller_php', methods: ['POST'])]
    public function applyDiscount(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $price = $data['price'] ?? null;
        $discountType = $data['discountType'] ?? null;
        $discountValue = $data['discountValue'] ?? null;
        $quantity = $data['quantity'] ?? 1;
        if ($price === null || $discountType === null) {
            return new JsonResponse(['error' => 'Invalid input'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $strategy = $this->factory->create($discountType, (float)$discountValue, (int)$quantity);
        } catch (\Throwable $th) {
            return new JsonResponse(['error' => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $context = new DiscountContext();
        $context->setStrategy($strategy);

        $discounted = $context->applyDiscount((float)$price);

        return new JsonResponse([
            'originalPrice' => $price,
            'discount' => $context->getLabel(),
            'finalPrice' => $discounted
        ]);
    }
}
