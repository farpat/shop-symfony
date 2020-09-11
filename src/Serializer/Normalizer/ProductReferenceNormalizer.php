<?php

namespace App\Serializer\Normalizer;

use App\Entity\Image;
use App\Entity\ProductReference;
use App\Services\Support\Arr;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductReferenceNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
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
     * @param ProductReference $object
     * @param null $format
     * @param array $context
     *
     * @return array
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $productUrl = $this->urlGenerator->generate('app_front_product_show', [
            'categorySlug' => $object->getProduct()->getCategory()->getSlug(),
            'categoryId'   => $object->getProduct()->getCategory()->getId(),
            'productSlug'  => $object->getProduct()->getSlug(),
            'productId'    => $object->getProduct()->getId()
        ]);

        return array_merge(
            Arr::get(['filled_product_fields', 'id', 'label', 'unit_price_excluding_taxes', 'unit_price_including_taxes'],
                $object),
            [
                'main_image' => $this->getImageInArray($object->getMainImage()),
                'images'    => array_map(fn($image) => $this->getImageInArray($image),
                    $object->getImages()->toArray()),
                'url'       => $productUrl . '#' . $object->getId(),
            ]
        );
    }

    private function getImageInArray(?Image $image)
    {
        if ($image === null) {
            return null;
        }

        return Arr::get(['id', 'url', 'alt', 'url_thumbnail', 'alt_thumbnail'], $image);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof ProductReference && $format === 'json';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
