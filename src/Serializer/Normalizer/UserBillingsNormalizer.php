<?php

namespace App\Serializer\Normalizer;

use App\Entity\Billing;
use App\Services\Support\Arr;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserBillingsNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param Billing $object
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return array_merge(
            Arr::get(['number', 'status', 'total_amount_including_taxes'], $object),
            [
                'address' => $object->getAddressText()
            ]
        );
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Billing && $format === 'billings-json';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
