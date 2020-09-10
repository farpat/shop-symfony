<?php

namespace App\Serializer\Normalizer;

use App\Entity\ProductField;
use App\Services\Support\Arr;
use App\Services\Support\Str;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductFieldNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param ProductField $object
     * @param null $format
     * @param array $context
     *
     * @return array
     * @throws ExceptionInterface
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        return array_merge(
            Arr::get(['label', 'type'], $object),
            [
                'key' => Str::getSnakeCase($object->getLabel()) . '-' . $object->getId(),
            ]
        );
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof ProductField;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
