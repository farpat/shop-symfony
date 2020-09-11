<?php

namespace App\Serializer\Normalizer;

use App\Entity\Image;
use App\Entity\Product;
use App\Entity\ProductReference;
use App\Services\Support\Arr;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{

    /**
     * @var UrlGeneratorInterface
     */
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
    public function normalize($object, string $format = null, array $context = []): array
    {
        return array_merge(
            Arr::get(['id', 'label', 'slug', 'excerpt', 'min_unit_price_including_taxes'], $object),
            [
                'url'        => $this->urlGenerator->generate('app_front_product_show', [
                    'productId'    => $object->getId(),
                    'productSlug'  => $object->getSlug(),
                    'categoryId'   => $object->getCategory()->getId(),
                    'categorySlug' => $object->getCategory()->getSlug()
                ]),
                'image'      => $this->getImageInArray($object->getMainImage()),
                'references' => $this->getReferencesInArray($object->getProductReferences()->toArray())
            ]
        );
    }

    public function getImageInArray(?Image $image): ?array
    {
        if ($image === null) {
            return null;
        }

        return Arr::get(['url_thumbnail', 'alt_thumbnail'], $image);
    }

    /**
     * @param ProductReference[] $references
     */
    public function getReferencesInArray(array $references): array
    {
        return array_map(
            fn($productReference) => ['filled_product_fields' => $productReference->getFilledProductFields()],
            $references
        );
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Product && $format === 'json';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
