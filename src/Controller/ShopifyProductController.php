<?php

namespace App\Controller;
use App\Service\Shopify\ShopifyApiClient;
use PHPUnit\Util\Json;
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
}