<?php

namespace App\Services\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture as FixturesBundleFixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Console\Output\ConsoleOutput;
use Faker\Generator;

abstract class Fixture extends FixturesBundleFixture
{
    /**
     * @var Generator
     */
    protected $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    protected function makeImage (ObjectManager $manager): Image
    {
        $id = random_int(1, 100);
        $normalSize = [1000, 400];
        $thumbSize = [300, 120];

        $url = "https://picsum.photos/id/{$id}/{$normalSize[0]}/{$normalSize[1]}/";
        $urlThumbnail = "https://picsum.photos/id/{$id}/{$thumbSize[0]}/{$thumbSize[1]}/";
        $alt = $this->faker->sentence;

        $image = (new Image)
            ->setUrl($url)
            ->setAlt($alt)
            ->setUrlThumbnail($urlThumbnail)
            ->setAltThumbnail($alt);

        $manager->persist($image);

        return $image;
    }
}
