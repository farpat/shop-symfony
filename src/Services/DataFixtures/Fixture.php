<?php

namespace App\Services\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture as FixturesBundleFixture;
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
}
