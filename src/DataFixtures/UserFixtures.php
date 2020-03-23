<?php

namespace App\DataFixtures;

use App\Services\DataFixtures\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\{Address, User};
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{

    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $usersCount = 10;

        for ($i = 0; $i < $usersCount; $i++) {
            $user = $this->makeUser($i, $manager);
            $addresses = $this->makeAddresses($user, $manager);
        }

        $manager->flush();
    }

    /**
     * @return Address[]
     */
    private function makeAddresses(User $user, ObjectManager $manager): array
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

            $addresses[] = $address;

            $manager->persist($address);
        }

        return $addresses;
    }

    private function makeUser(int $i, ObjectManager $manager): User

    {
        $user = new User;

        $user
            ->setName($this->faker->name)
            ->setRoles($this->faker->boolean(15) ? ['ROLE_ADMIN', 'ROLE_ADMIN'] : ['ROLE_USER'])
            ->setEmail("user$i@local.dev")
            ->setPassword($this->encoder->encodePassword($user, 'secret'))
            ->setEmailVerifiedAt($this->faker->dateTime())
            ->setRememberToken(null);

        $manager->persist($user);

        return $user;
    }


    public function getOrder()
    {
        return 4;
    }
}
