<?php

namespace App\Service\Shopify;

use GuzzleHttp\ClientInterface;

class ShopifyApiClient
{
    private ClientInterface $client;
    private string $shopifyApiVersion;
    private string $storeUrl;
    private string $accessToken;
    public function __construct(
        ClientInterface $client,
        string $shopifyApiVersion,
        string $storeUrl,
        string $accessToken
    ) {
        $this->client = $client;
        $this->shopifyApiVersion = $shopifyApiVersion;
        $this->storeUrl = $storeUrl;
        $this->accessToken = $accessToken;
    }

    public function getProducts(): array
    {
        $response = $this->client->request('GET', "{$this->storeUrl}/admin/api/{$this->shopifyApiVersion}/products.json", [
            'headers' => [
                'X-Shopify-Access-Token' => $this->accessToken,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Failed to fetch products from Shopify');
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['products'] ?? [];
    }

    public function getProductById(int $productId): array
    {
        $response = $this->client->request('GET', "{$this->storeUrl}/admin/api/{$this->shopifyApiVersion}/products/{$productId}.json", [
            'headers' => [
                'X-Shopify-Access-Token' => $this->accessToken,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException("Failed to fetch product with ID {$productId} from Shopify");
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['product'] ?? [];
    }

    public function updateVariantPrice(int $variantId, float $price): array
    {
        dump("updating variant price", $variantId, $price);
        $response = $this->client->request('PUT', "{$this->storeUrl}/admin/api/{$this->shopifyApiVersion}/variants/{$variantId}.json", [
            'headers' => [
                'X-Shopify-Access-Token' => $this->accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'variant' => [
                    'id' => $variantId,
                    'price' => $price,
                ],
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException("Failed to update variant price for ID {$variantId}");
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['variant'] ?? [];
    }

}
