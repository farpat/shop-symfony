<?php

namespace App\DataFixtures;

use App\Entity\{Address, Billing, Cart, Orderable, OrderItem, ProductReference, User};
use App\Services\DataFixtures\Fixture;
use DateTime;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    protected ?ObjectManager             $entityManager = null;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->entityManager = $manager;

        $usersCount = 10;
        $allProductReferences = $manager->getRepository(ProductReference::class)->findAll();

        for ($i = 0; $i < $usersCount; $i++) {
            $manager->persist($user = $this->makeUser($i));

            $addresses = $this->makeAddresses($user);
            $user->setDeliveryAddress($addresses[0]);
            $this->makeBillings($user, $addresses, $allProductReferences);
            $this->makeCart($user, $addresses, $allProductReferences);

        }

        $manager->flush();
    }

    private function makeUser(int $i): User
    {
        $i++;
        $user = new User;

        $user
            ->setName($this->faker->name)
            ->setRoles($this->faker->boolean(30) ? ['ROLE_ADMIN', 'ROLE_USER'] : ['ROLE_USER'])
            ->setEmail("user$i@local.dev")
            ->setPassword($this->encoder->encodePassword($user, 'secret'))
            ->setEmailVerifiedAt($this->faker->dateTime())
            ->setRememberToken(null);

        return $user;
    }

    /**
     * @return Address[]
     */
    private function makeAddresses(User $user): array
    {
        $addresses = [];

        for ($i = 0; $i < 2; $i++) {
            $line1 = $this->faker->streetAddress;
            $line2 = $this->faker->boolean(70) ? ucfirst($this->faker->words(3, true)) : '';
            $postalCode = $this->faker->postcode;
            $city = $this->faker->city;
            $country = 'France';
            $countryCode = 'FR';
            $latitude = $this->faker->latitude;
            $longitude = $this->faker->longitude;
            $text = $line1 . ' ' . $postalCode . ' ' . $city . ', ' . $country;


            $address = (new Address)
                ->setUser($user)
                ->setLine1($line1)
                ->setLine2($line2)
                ->setPostalCode($postalCode)
                ->setCity($city)
                ->setCountry($country)
                ->setCountryCode($countryCode)
                ->setLatitude($latitude)
                ->setLongitude($longitude)
                ->setText($text);

            $this->entityManager->persist($address);

            $addresses[] = $address;
        }

        return $addresses;
    }

    /**
     * @param User $user
     * @param array $addresses
     * @param array $allProductReferences
     *
     * @return Billing[]
     * @throws Exception
     */
    private function makeBillings(User $user, array $addresses, array $allProductReferences): array
    {
        static $currentBillingNumber = 0;
        $billings = [];

        $billingsCount = random_int(3, 7);

        for ($i = 0; $i < $billingsCount; $i++) {
            $now = new DateTime;

            $billing = (new Billing)
                ->setUser($user)
                ->setComment($this->faker->boolean(25) ? $this->faker->words(10, true) : null)
                ->setDeliveryAddress($addresses[array_rand($addresses)])
                ->setDeliveryAddress(null) //To set $billing->delivery_address to null
                ->setNumber($now->format('Y-m') . '-' . (++$currentBillingNumber))
                ->setStatus(Billing::DELIVRED_STATUS);

            $items = $this->makeOrderItems(random_int(1, 5), $billing, $allProductReferences);

            $billing
                ->setItemsCount(count($items))
                ->setTotalAmountExcludingTaxes(array_reduce($items, function (float $acc, OrderItem $item) {
                    $acc += $item->getAmountExcludingTaxes();
                    return $acc;
                }, 0))
                ->setTotalAmountIncludingTaxes(array_reduce($items, function (float $acc, OrderItem $item) {
                    $acc += $item->getAmountIncludingTaxes();
                    return $acc;
                }, 0));

            $this->entityManager->persist($billing);

            $billings[] = $billing;
        }

        return $billings;
    }

    /**
     * @param int $itemsCount
     * @param Orderable $orderable
     * @param ProductReference[] $productReferences
     *
     * @return array
     * @throws Exception
     */
    private function makeOrderItems(int $itemsCount, Orderable $orderable, array $productReferences): array
    {
        $orderItems = [];
        shuffle($productReferences);
        $productReferencesToUse = array_slice($productReferences, 0, $itemsCount);

        for ($i = 0; $i < $itemsCount; $i++) {
            $quantity = random_int(1, 10);

            $amountExcludingTaxes = $quantity * $productReferencesToUse[$i]->getUnitPriceExcludingTaxes();
            $amountIncludingTaxes = $quantity * $productReferencesToUse[$i]->getUnitPriceIncludingTaxes();

            $orderItem = (new OrderItem)
                ->setOrderable($orderable)
                ->setQuantity($quantity)
                ->setProductReference($productReferencesToUse[$i])
                ->setAmountExcludingTaxes($amountExcludingTaxes)
                ->setAmountIncludingTaxes($amountIncludingTaxes);

            $this->entityManager->persist($orderItem);

            $orderItems[] = $orderItem;
        }

        return $orderItems;
    }

    /**
     * @param User $user
     * @param array $addresses
     * @param array $allProductReferences
     *
     * @return Cart
     * @throws Exception
     */
    private function makeCart(User $user, array $addresses, array $allProductReferences)
    {
        $cart = (new Cart)
            ->setUpdatedAt(new DateTime())
            ->setComment($this->faker->boolean(25) ? $this->faker->words(5, true) : null)
            ->setDeliveryAddress($user->getDeliveryAddress());

        $user->addCart($cart);

        $items = $this->makeOrderItems(random_int(1, 5), $cart, $allProductReferences);

        $cart
            ->setItemsCount(count($items))
            ->setTotalAmountExcludingTaxes(array_reduce($items, function (float $acc, OrderItem $item) {
                $acc += $item->getAmountExcludingTaxes();
                return $acc;
            }, 0))
            ->setTotalAmountIncludingTaxes(array_reduce($items, function (float $acc, OrderItem $item) {
                $acc += $item->getAmountIncludingTaxes();
                return $acc;
            }, 0));

        $this->entityManager->persist($cart);

        return $cart;
    }

    public function getOrder()
    {
        return 4;
    }
}
