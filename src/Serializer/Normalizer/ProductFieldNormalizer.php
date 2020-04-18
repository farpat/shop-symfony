<?php

namespace App\Serializer\Normalizer;

use App\Entity\ProductField;
use App\Services\Support\Str;
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
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize ($object, $format = null, array $context = []): array
    {
        return [
            'label' => $object->getLabel(),
            'key'   => Str::getSnakeCase($object->getLabel()) . '-' . $object->getId(),
            'type'  => $object->getType(),
        ];
    }

    public function supportsNormalization ($data, $format = null): bool
    {
        return $data instanceof ProductField;
    }

    public function hasCacheableSupportsMethod (): bool
    {
        return true;
    }
}
