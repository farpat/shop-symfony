<?php

namespace App\Serializer\Normalizer;

use App\Entity\Product;
use App\Services\Support\Arr;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductSearchedNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Product $object
     * @param null $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        return array_merge(
            Arr::get(['id', 'label', 'minUnitPriceIncludingTaxes'], $object),
            [
                'image' => $object->getMainImage() ? $object->getMainImage()->getUrlThumbnail() : null,
                'url'   => $this->urlGenerator->generate('app_front_product_show', [
                    'categorySlug' => $object->getCategory()->getSlug(),
                    'categoryId'   => $object->getCategory()->getId(),
                    'productSlug'  => $object->getSlug(),
                    'productId'    => $object->getId()
                ]),
            ]
        );
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Product && $format === 'search';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
