<?php

namespace App\Services\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture as FixturesBundleFixture;
use Faker\Factory;
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

    protected function makeImage(): Image
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

        $this->entityManager->persist($image);

        return $image;
    }
}
