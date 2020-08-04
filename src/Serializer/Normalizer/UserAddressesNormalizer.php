<?php

namespace App\Serializer\Normalizer;

use App\Entity\Address;
use App\Entity\User;
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
    public function normalize($object, $format = null, array $context = []): array
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

    public function getAddressInArray(?Address $address)
    {
        if ($address === null) {
            return null;
        }

        return [
            'id'           => $address->getId(),
            'text'         => $address->getText(),
            'line1'        => $address->getLine1(),
            'line2'        => $address->getLine2(),
            'postal_code'  => $address->getPostalCode(),
            'city'         => $address->getCity(),
            'country'      => $address->getCountry(),
            'country_code' => $address->getCountryCode(),
            'latitude'     => $address->getLatitude(),
            'longitude'    => $address->getLongitude()
        ];
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
