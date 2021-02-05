<?php

namespace App\Serializer\Normalizer;

use App\Entity\Address;
use App\Entity\User;
use App\Services\Support\Arr;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserAddressesNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param User $object
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $deliveryAddressIndex = null;

        $addresses = [];
        foreach ($object->getAddresses()->toArray() as $index => $address) {
            if ($address === $object->getDeliveryAddress()) {
                $deliveryAddressIndex = $index;
            }

            $addresses[] = $this->getAddressInArray($address);
        }

        return [
            'delivery_address_index' => $deliveryAddressIndex,
            'addresses'              => $addresses
        ];
    }

    public function getAddressInArray(?Address $address): ?array
    {
        if ($address === null) {
            return null;
        }

        return array_merge(
            Arr::get([
                'id',
                'text',
                'line1',
                'line2',
                'postal_code',
                'city',
                'country',
                'country_code',
                'latitude',
                'longitude',
            ], $address),
            ['status' => null]
        );
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof User && $format === 'addresses-json';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
