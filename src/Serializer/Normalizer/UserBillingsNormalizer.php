<?php

namespace App\Serializer\Normalizer;

use App\Entity\Billing;
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
    public function normalize($object, $format = null, array $context = []): array
    {
        return [
            'number'                      => $object->getNumber(),
            'status'                      => $object->getStatus(),
            'address'                     => $object->getAddressText(),
            'total_price_including_taxes' => $object->getTotalAmountIncludingTaxes()
        ];
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
