<?php

namespace App\Services\DataFixtures;

use App\Entity\Image;
use App\Services\Support\Str;
use Doctrine\Bundle\FixturesBundle\Fixture as FixturesBundleFixture;
use Faker\Factory;
use Faker\Generator;

abstract class Fixture extends FixturesBundleFixture
{
    protected Generator $faker;

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
        $alt = $this->faker->words(3, true);

        $image = (new Image)
            ->setLabel(Str::getSnakeCase($this->faker->words(3, true)) . '.jpg')
            ->setUrl($url)
            ->setAlt($alt)
            ->setUrlThumbnail($urlThumbnail)
            ->setAltThumbnail($alt);

        return $image;
    }
}
