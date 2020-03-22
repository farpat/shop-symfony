<?php

namespace App\Services\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture as FixturesBundleFixture;
use Faker\Factory;
use Symfony\Component\Console\Output\ConsoleOutput;
use Faker\Generator;

class Fixture extends FixturesBundleFixture
{

    /**
     * @var Generator
     */
    protected $faker;
    /**
     * @var ConsoleOutput
     */
    protected $console;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
        $this->console = new ConsoleOutput();
    }

    protected function startTime(string $label): float
    {
        $this->console->write("<comment>Start: $label\n</comment>");
        return microtime(true);
    }

    protected function endTime(float $start): void
    {
        $end = microtime(true);
        $this->console->write(sprintf("<info> => Finish: %s seconds\n</info>", round($end - $start, 2)));
    }
}
