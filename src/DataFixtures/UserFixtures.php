<?php

namespace App\DataFixtures;

use App\Entity\{Address, Billing, Cart, Orderable, OrderItem, ProductReference, User};
use App\Services\DataFixtures\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    public function __construct (UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->encoder = $encoder;
    }

    public function load (ObjectManager $manager)
    {
        $usersCount = 10;
        $allProductReferences = $manager->getRepository(ProductReference::class)->findAll();

        for ($i = 0; $i < $usersCount; $i++) {
            $user = $this->makeUser($i);
            $addresses = $this->makeAddresses($user, $manager);
            $this->makeBillings($user, $addresses, $allProductReferences, $manager);
            $this->makeCart($user, $addresses, $allProductReferences, $manager);

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @return Address[]
     */
    private function makeAddresses (User $user, ObjectManager $manager): array
    {
        $addresses = [];

        for ($i = 0; $i < 2; $i++) {
            $line1 = $this->faker->streetAddress;
            $line2 = $this->faker->boolean(70) ? ucfirst($this->faker->words(3, true)) : '';
            $postalCode = $this->faker->postcode;
            $city = $this->faker->city;
            $country = $this->faker->country;
            $latitude = $this->faker->latitude;
            $longitude = $this->faker->longitude;
            $text = $line1 . ' ' . $line2 . ' ' . $postalCode . ' ' . $city . ', ' . $country;


            $address = (new Address)
                ->setUser($user)
                ->setLine1($line1)
                ->setLine2($line2)
                ->setPostalCode($postalCode)
                ->setCity($city)
                ->setCountry($country)
                ->setLatitude($latitude)
                ->setLongitude($longitude)
                ->setText($text);

            $manager->persist($address);

            $addresses[] = $address;
        }

        return $addresses;
    }

    private function makeUser (int $i): User
    {
        $i++;
        $user = new User;

        $user
            ->setName($this->faker->name)
            ->setRoles($this->faker->boolean(15) ? ['ROLE_ADMIN', 'ROLE_ADMIN'] : ['ROLE_USER'])
            ->setEmail("user$i@local.dev")
            ->setPassword($this->encoder->encodePassword($user, 'secret'))
            ->setEmailVerifiedAt($this->faker->dateTime())
            ->setRememberToken(null);

        return $user;
    }

    public function getOrder ()
    {
        return 4;
    }

    /**
     * @param User $user
     * @param array $addresses
     * @param array $allProductReferences
     * @param ObjectManager $manager
     *
     * @return Cart
     * @throws \Exception
     */
    private function makeCart (User $user, array $addresses, array $allProductReferences, ObjectManager $manager)
    {
        $now = new \DateTime;

        $cart = (new Cart)
            ->setUser($user)
            ->setUpdatedAt($now)
            ->setComment($this->faker->boolean(25) ? $this->faker->sentence : null)
            ->setDeliveredAddress($addresses[random_int(0, count($addresses) - 1)]);

        $items = $this->makeOrderItems(random_int(1, 5), $cart, $allProductReferences, $manager);

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

        $manager->persist($cart);

        return $cart;
    }

    /**
     * @param User $user
     * @param array $addresses
     * @param array $allProductReferences
     * @param ObjectManager $manager
     *
     * @return Billing[]
     * @throws \Exception
     */
    private function makeBillings (User $user, array $addresses, array $allProductReferences, ObjectManager $manager): array
    {
        static $currentBillingNumber = 0;
        $billings = [];

        $billingsCount = random_int(1, 4);

        for ($i = 0; $i < $billingsCount; $i++) {
            $now = new \DateTime;

            $billing = (new Billing)
                ->setUser($user)
                ->setUpdatedAt($now)
                ->setComment($this->faker->boolean(25) ? $this->faker->sentence : null)
                ->setDeliveredAddress($addresses[random_int(0, count($addresses) - 1)])
                ->setNumber($now->format('Y-m') . '-' . (++$currentBillingNumber))
                ->setStatus(Billing::DELIVRED_STATUS);

            $items = $this->makeOrderItems(random_int(1, 5), $billing, $allProductReferences, $manager);

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

            $manager->persist($billing);

            $billings[] = $billing;
        }

        return $billings;
    }

    /**
     * @param int $itemsCount
     * @param Orderable $orderable
     * @param ProductReference[] $productReferences
     * @param ObjectManager $manager
     *
     * @return array
     * @throws \Exception
     */
    private function makeOrderItems (int $itemsCount, Orderable $orderable, array $productReferences, ObjectManager $manager): array
    {
        $orderItems = [];
        $productReferencesCount = count($productReferences);
        shuffle($productReferences);
        /** @var ProductReference[] $productReferencesToUse */
        $productReferencesToUse = array_slice($productReferences, 0, $productReferencesCount);

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

            $manager->persist($orderItem);

            $orderItems[] = $orderItem;
        }

        return $orderItems;
    }
}
