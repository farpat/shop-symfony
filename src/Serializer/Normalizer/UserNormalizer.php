<?php

namespace App\Serializer\Normalizer;

use App\Entity\User;
use App\Services\Support\Arr;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param User $object
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return Arr::get(['email', 'name'], $object);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof User && $format === 'json';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
