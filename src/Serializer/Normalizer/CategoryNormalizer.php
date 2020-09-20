<?php

namespace App\Serializer\Normalizer;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\ProductField;
use App\Services\Support\Arr;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CategoryNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param Category $object
     * @param null $format
     * @param array $context
     *
     * @return array
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return array_merge(
            Arr::get(['id', 'label', 'nomenclature', 'slug', 'description', 'is_last_level'], $object),
            [
                'image'          => $this->getImageInArray($object->getImage()),
                'product_fields' => array_map(
                    fn(ProductField $productField) => array_merge(Arr::get(['id', 'type', 'label', 'is_required'],
                        $productField), ['status' => null]),
                    $object->getProductFields()->toArray()
                )
            ]
        );
    }

    private function getImageInArray(?Image $image): ?array
    {
        if ($image === null) {
            return null;
        }

        return Arr::get(['id', 'url', 'alt', 'url_thumbnail', 'alt_thumbnail', 'label'], $image);
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Category && $format === 'json';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
