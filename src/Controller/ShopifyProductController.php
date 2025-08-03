<?php

namespace App\Controller;

use App\Service\Discount\DiscountContext;
use App\Service\Discount\FixedAmountDiscountStrategy;
use App\Service\Shopify\ShopifyApiClient;
use PHPUnit\Util\Json;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class ShopifyProductController
{
    #[Route('/products', name: 'app_shopify_product_controller_php', methods: ['GET'])]
    public function getProducts(ShopifyApiClient $shopifyApiClient): JsonResponse
    {
        try {
            $products = $shopifyApiClient->getProducts();
        } catch (\Throwable $th) {
            return new JsonResponse(['error' => 'Failed to fetch products from Shopify: ' . $th->getMessage()], 500);
        }
        return new JsonResponse($products, 200);
    }

    #[Route('/products/{id}', name: 'app_shopify_product_detail_controller_php', methods: ['GET'])]
    public function getProductById(int $id, ShopifyApiClient $shopifyApiClient): JsonResponse
    {
        try {
            $product = $shopifyApiClient->getProductById($id);
        } catch (\Throwable $th) {
            return new JsonResponse(['error' => 'Failed to fetch product with id ' . $id . ': ' . $th->getMessage()], 500);
        }

        if (empty($product)) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        return new JsonResponse($product, 200);
    }

    #[Route('/products/apply-discount/{id}', name: 'app_shopify_product_update_price_controller_php', methods: ['PUT'])]
    public function updateVariantPrice(int $id, DiscountContext $discountContext, ShopifyApiClient $shopifyApiClient, LoggerInterface $logger): JsonResponse
    {
        $logger->info("updating variant price", ['id' => $id]);
        try {
            $product = $shopifyApiClient->getProductById($id);
            $logger->info("fetched product", ['product' => $product]);
            $variant = $product['product']['variants'][0]?? null;
            $logger->info("variant fetched", ['variant' => $variant]);
            $originalPrice = (float)$variant['price'] ?? null;
            $logger->info("original price", ['price' => $originalPrice]);

            $discountContext->setStrategy(new FixedAmountDiscountStrategy(100));
            $discountedPrice = $discountContext->applyDiscount($originalPrice);
            $updatedVariant = $shopifyApiClient->updateVariantPrice($variant['id'], $discountedPrice);
            $logger->info("variant price updated", ['id' => $variant['id'], 'price' => $discountedPrice]);
        } catch (\Throwable $th) {
            return new JsonResponse(['error' => 'Failed to update price for variant with id ' . $id . ': ' . $th->getMessage()], 500);
        }

        if (empty($variant)) {
            return new JsonResponse(['error' => 'Variant not found'], 404);
        }

        return new JsonResponse($updatedVariant, 200);
    }
}