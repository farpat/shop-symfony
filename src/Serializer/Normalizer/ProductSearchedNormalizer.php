<?php

namespace App\Serializer\Normalizer;

use App\Entity\Product;
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
        $url = $this->urlGenerator->generate('app_product_show', [
            'categorySlug' => $object->getCategory()->getSlug(),
            'categoryId' => $object->getCategory()->getId(),
            'productSlug' => $object->getSlug(),
            'productId' => $object->getId()
        ]);

        return [
            'id' => $object->getId(),
            'label' => $object->getLabel(),
            'image' => $object->getMainImage() ? $object->getMainImage()->getUrlThumbnail() : null,
            'url' => $url,
            'minUnitPriceIncludingTaxes' => $object->getMinUnitPriceIncludingTaxes()
        ];
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
