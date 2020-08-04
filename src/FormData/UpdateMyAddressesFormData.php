<?php

namespace App\FormData;


use App\Entity\Address;
use App\Entity\User;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Farpat\Api\Api;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UpdateMyAddressesFormData
{
    private ?int $deliveryAddressIndex;

    private array                   $addresses;
    private string                  $googleGeocodeKey;
    private static array            $geocodesByIndex   = [];
    private EntityManagerInterface  $entityManager;
    private ?AddressRepository      $addressRepository = null;

    public function __construct(array $data, string $googleGeocodeKey, EntityManagerInterface $entityManager)
    {
        $this->googleGeocodeKey = $googleGeocodeKey;
        $this->entityManager = $entityManager;

        $this
            ->setDeliveryAddressIndex($data['delivery_address_index'])
            ->setAddresses($data['addresses']);
    }

    /**
     * @param ExecutionContextInterface $context
     * @param $payload
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $this->checkDeliveryAddressIndex($context);
        foreach ($this->getAddresses() as $index => $address) {
            $this->checkAddress($context, $index, $address);
        }
    }

    private function checkAddress(ExecutionContextInterface $context, int $index, array $address)
    {
        if (isset($address['is_deleted']) && $address['is_deleted'] === true) {
            return;
        }

        //CHECK LATITUDE, LONGITUDE
        if (isset($address['text']) && is_string($address['text']) && $address['text'] !== '') {
            $result = (new Api)
                ->setUrl('https://maps.googleapis.com/maps/api')
                ->get('geocode/json', [
                    'address' => urlencode($address['text']),
                    'key'     => $this->googleGeocodeKey
                ]);
            if ($result->status !== 'OK') {
                $context->buildViolation('This address doesn\'t exist (geocoding verification)')
                    ->atPath("addresses.$index")
                    ->addViolation();
            } else {
                self::$geocodesByIndex[$index] = [
                    'latitude'  => $result->results[0]->geometry->location->lat,
                    'longitude' => $result->results[0]->geometry->location->lng
                ];
            }
        } else {
            $context->buildViolation('You should fill an address or delete this address')
                ->atPath("addresses.$index")
                ->addViolation();
        }
    }

    private function checkDeliveryAddressIndex(ExecutionContextInterface $context)
    {
        $deliveryAddressIndex = $this->getDeliveryAddressIndex();
        $addresses = $this->getAddresses();
        $addressesCount = array_reduce($addresses, function (int $accumulator, array $item) {
            if (!isset($item['is_deleted']) || $item['is_deleted'] === false) {
                $accumulator++;
            }

            return $accumulator;
        }, 0);

        if ($addressesCount === 0) {
            return;
        }

        if ($deliveryAddressIndex === null && $addressesCount > 0) {
            $context->buildViolation('You must select an address')
                ->atPath('delivery_address_index')
                ->addViolation();

            return;
        }

        if ($deliveryAddressIndex !== null && !isset($addresses[$deliveryAddressIndex])) {
            $context->buildViolation('Bad index')
                ->atPath('delivery_address_index')
                ->addViolation();

            return;
        }

        $selectedAddress = $addresses[$deliveryAddressIndex];
        if (isset($selectedAddress['is_deleted']) && $selectedAddress['is_deleted'] === true) {
            $context->buildViolation('The selected address is deleted')
                ->atPath('delivery_address_index')
                ->addViolation();
        }
    }

    private function getAddress(int $id, User $user): Address
    {
        $address = $this->addressRepository->findOneBy(['id' => $id, 'user' => $user]);

        if ($address === null) {
            throw new \Exception("Address << $id >> doesn't exists!");
        }

        return $address;
    }

    public function updateUser(User $user): User
    {
        $this->addressRepository = $this->entityManager->getRepository(Address::class);

        $addresses = $this->getAddresses();

        foreach ($addresses as $index => $addressData) {
            if ($addressData['id'] !== null) {
                if (isset($addressData['is_deleted']) && $addressData['is_deleted']) {
                    //to delete
                    $addressToUDelete = $this->getAddress($addressData['id'], $user);
                    $this->entityManager->remove($addressToUDelete);
                } else {
                    //to update
                    $addressToUpdate = $this->getAddress($addressData['id'], $user);

                    $addressToUpdate
                        ->setText($addressData['text'])
                        ->setLine1($addressData['line1'])
                        ->setLine2($addressData['line2'])
                        ->setPostalCode($addressData['postal_code'])
                        ->setCity($addressData['city'])
                        ->setCountry($addressData['country'])
                        ->setCountryCode($addressData['country_code'])
                        ->setLatitude(self::$geocodesByIndex[$index]['latitude'])
                        ->setLongitude(self::$geocodesByIndex[$index]['longitude']);

                    if ($index === $this->getDeliveryAddressIndex()) {
                        $user->setDeliveryAddress($addressToUpdate);
                    }
                }
            } else {
                //to add
                $addressToAdd = (new Address())
                    ->setUser($user)
                    ->setText($addressData['text'])
                    ->setLine1($addressData['line1'])
                    ->setLine2($addressData['line2'])
                    ->setPostalCode($addressData['postal_code'])
                    ->setCity($addressData['city'])
                    ->setCountry($addressData['country'])
                    ->setCountryCode($addressData['country_code'])
                    ->setLatitude(self::$geocodesByIndex[$index]['latitude'])
                    ->setLongitude(self::$geocodesByIndex[$index]['longitude']);

                if ($index === $this->getDeliveryAddressIndex()) {
                    $user->setDeliveryAddress($addressToAdd);
                }

                $this->entityManager->persist($addressToAdd);
            }
        }

        return $user;
    }

    public function setDeliveryAddressIndex(?int $deliveryAddressIndex): self
    {
        $this->deliveryAddressIndex = $deliveryAddressIndex;
        return $this;
    }

    /**
     * @return array[]
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }

    public function setAddresses(array $addresses): UpdateMyAddressesFormData
    {
        $this->addresses = $addresses;
        return $this;
    }

    public function getDeliveryAddressIndex(): ?int
    {
        return $this->deliveryAddressIndex;
    }
}