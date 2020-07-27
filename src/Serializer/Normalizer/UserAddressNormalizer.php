<?php

namespace App\Serializer\Normalizer;

use App\Entity\Address;
use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserAddressNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param User $object
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        return [
            'delivery_address' => $this->getAddressInArray($object->getDeliveryAddress()),
            'addresses'        => array_map(fn(Address $address) => $this->getAddressInArray($address),
                $object->getAddresses())
        ];
    }

    public function getAddressInArray(?Address $address)
    {
        if ($address === null) {
            return null;
        }

        return [
            'id'   => $address->getId(),
            'text' => $address->getText()
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof User && $format === 'addresses';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
