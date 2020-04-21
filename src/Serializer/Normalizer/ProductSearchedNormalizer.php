<?php

namespace App\Serializer\Normalizer;

use App\Entity\Product;
use App\Services\Shop\ProductService;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductSearchedNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private ProductService $productService;

    public function __construct (ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @param Product $object
     * @param null $format
     * @param array $context
     *
     * @return array
     */
    public function normalize ($object, $format = null, array $context = []): array
    {
        return [
            'id'                             => $object->getId(),
            'label'                          => $object->getLabel(),
            'image'                          => $object->getMainImage() ? $object->getMainImage()->getUrlThumbnail() : null,
            'url'                            => $this->productService->getShowUrl($object),
            'min_unit_price_including_taxes' => $object->getMinUnitPriceIncludingTaxes()
        ];
    }

    public function supportsNormalization ($data, $format = null): bool
    {
        return $data instanceof Product && $format === 'search';
    }

    public function hasCacheableSupportsMethod (): bool
    {
        return true;
    }
}
