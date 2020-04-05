<?php

namespace App\Serializer\Normalizer;

use App\Entity\Product;
use App\Services\Shop\ProductService;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
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
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize ($object, $format = null, array $context = []): array
    {
        $image = $object->getMainImage();
        $category = $object->getCategory();

        $normalizer = new GetSetMethodNormalizer();

        return [
            'id'                         => $object->getId(),
            'url'                        => $this->productService->getShowUrl($object),
            'label'                      => $object->getLabel(),
            'slug'                       => $object->getSlug(),
            'excerpt'                    => $object->getExcerpt(),
            'minUnitPriceExcludingTaxes' => $object->getMinUnitPriceIncludingTaxes(),
            'mainImage'                  => $image ? $normalizer->normalize($image, null, [
                AbstractNormalizer::ATTRIBUTES => ['urlThumbnail', 'altThumbnail'],
            ]) : null,
            'category'                   => $category ? $normalizer->normalize($category, null, [
                AbstractNormalizer::ATTRIBUTES => ['slug', 'level'],
            ]) : null,
        ];
    }

    public function supportsNormalization ($data, $format = null): bool
    {
        return $data instanceof Product;
    }

    public function hasCacheableSupportsMethod (): bool
    {
        return true;
    }
}
