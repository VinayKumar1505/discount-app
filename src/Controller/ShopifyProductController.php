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
            $variants = $product['variants']?? null;
            $results = [];
            foreach ($variants as $variant) {
                $originalPrice = (float)$variant['price'] ?? null;
                $discountContext->setStrategy(new FixedAmountDiscountStrategy(100));
                $discountedPrice = $discountContext->applyDiscount($originalPrice);
                $updatedVariant = $shopifyApiClient->updateVariantPrice($variant['id'], $discountedPrice);
                $results[] = [
                    'original_price' => $originalPrice,
                    'discounted_price' => $discountedPrice,
                    'updated_variant' => $updatedVariant
                ];
            }
            return new JsonResponse(['variants_updated' => $results], 200);

            // $originalPrice = (float)$variant['price'] ?? null;
            // $discountContext->setStrategy(new FixedAmountDiscountStrategy(100));
            // $discountedPrice = $discountContext->applyDiscount($originalPrice);
            // $updatedVariant = $shopifyApiClient->updateVariantPrice($variant['id'], $discountedPrice);
        } catch (\Throwable $th) {
            return new JsonResponse(['error' => 'Failed to update price for variant with id ' . $id . ': ' . $th->getMessage()], 500);
        }

        if (empty($variant)) {
            return new JsonResponse(['error' => 'Variant not found'], 404);
        }

        // return new JsonResponse($updatedVariant, 200);
        // return new JsonResponse(['original_price' => $originalPrice, 'discounted_price' => $discountedPrice, 'updated_variant' => $updatedVariant], 200);
    }
}
