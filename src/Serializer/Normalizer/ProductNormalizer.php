<?php

namespace App\Serializer\Normalizer;

use App\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    public function __construct (UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
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
        return [
            'id'                             => $object->getId(),
            'url'                            => $this->urlGenerator->generate('app_product_show', [
                'productId'    => $object->getId(),
                'productSlug'  => $object->getSlug(),
                'categoryId'   => $object->getCategory()->getId(),
                'categorySlug' => $object->getCategory()->getSlug()
            ]),
            'label'                          => $object->getLabel(),
            'slug'                           => $object->getSlug(),
            'excerpt'                        => $object->getExcerpt(),
            'min_unit_price_excluding_taxes' => $object->getMinUnitPriceIncludingTaxes(),
            'image'                          => $object->getMainImage() ? [
                'url_thumbnail' => $object->getMainImage()->getUrlThumbnail(),
                'alt_thumbnail' => $object->getMainImage()->getAltThumbnail()
            ] : null,
            'references'                     => array_map(fn($productReference) => ['filled_product_fields' => $productReference->getFilledProductFields()], $object->getProductReferences()->toArray())
        ];
    }

    public function supportsNormalization ($data, $format = null): bool
    {
        return $data instanceof Product && $format === 'json';
    }

    public function hasCacheableSupportsMethod (): bool
    {
        return true;
    }
}
