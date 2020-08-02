<?php

namespace App\FormData;


use App\Entity\User;
use Farpat\Api\Api;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UpdateMyAddressesFormData
{
    private ?int $deliveryAddressIndex;

    private array  $addresses;
    private string $googleGeocodeKey;

    public function __construct(array $data, string $googleGeocodeKey)
    {
        $this->googleGeocodeKey = $googleGeocodeKey;

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
                $context->buildViolation('The address does not exists!')
                    ->atPath("addresses.$index")
                    ->addViolation();
            }
        } else {
            $context->buildViolation('The address is not in the right format')
                ->atPath("addresses.$index")
                ->addViolation();
        }
    }

    private function checkDeliveryAddressIndex(ExecutionContextInterface $context)
    {
        $deliveryAddressIndex = $this->getDeliveryAddressIndex();
        $addresses = $this->getAddresses();
        $addressesCount = count($addresses);

        if ($deliveryAddressIndex === null && $addressesCount > 0) {
            $context->buildViolation('No address selected')
                ->atPath('delivery_address_index')
                ->addViolation();

            return;
        }

        if ($deliveryAddressIndex !== null && ($deliveryAddressIndex > $addressesCount || $deliveryAddressIndex < 0)) {
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

    public function updateUser(User $user): User
    {
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