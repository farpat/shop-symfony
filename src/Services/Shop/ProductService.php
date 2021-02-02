<?php

namespace App\Services\Shop;


use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Services\ModuleService;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ProductService
{
    private ProductRepository $productRepository;
    private ModuleService     $moduleService;
    /**
     * @var CacheInterface|CacheItemPoolInterface
     */
    private CacheInterface    $cache;

    public function __construct(
        ProductRepository $productRepository,
        ModuleService $moduleService,
        CacheInterface $cache
    ) {
        $this->productRepository = $productRepository;
        $this->moduleService = $moduleService;
        $this->cache = $cache;
    }

    /**
     * @param int[] $ids
     * @return Product[]
     */
    public function getProductsForMenu(array $ids): array
    {
        return $this->productRepository->getProductsForMenu($ids);
    }

    /**
     * @return Product[]
     */
    public function getProductsInHome(): array
    {
        return $this->cache->get('product#getProductsInHome', function (ItemInterface $item) {
            $productIds = $this->moduleService->getParameter('home', 'products')->getValue();
            if (empty($productIds)) {
                return [];
            }
            return $this->productRepository->getProductsInHome($productIds);
        });
    }
}