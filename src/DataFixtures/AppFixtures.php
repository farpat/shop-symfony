<?php

namespace App\DataFixtures;

use App\Entity\Module;
use App\Entity\ModuleParameter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Output\ConsoleOutput;

class AppFixtures extends Fixture
{

    /**
     * @var Generator
     */
    private $faker;
    /**
     * @var ConsoleOutput
     */
    private $console;

    public function __construct ()
    {
        $this->faker = Factory::create('fr_FR');
        $this->console = new ConsoleOutput();
    }

    private function startTime (string $label): float
    {
        $this->console->write("<comment>Start: $label\n</comment>");
        return microtime(true);
    }

    private function endTime (float $start): void
    {
        $end = microtime(true);
        $this->console->write(sprintf("<info>=> Finish: %s seconds\n</info>", round($end - $start, 2)));
    }

    public function load (ObjectManager $manager)
    {
        $this->resetAndCreationOfModules($manager);
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }

    private function resetAndCreationOfModules (ObjectManager $manager)
    {
        $start = $this->startTime('Reset and Creation of modules');
        $manager->persist($module = (new Module)
            ->setLabel('home')
            ->setIsActive(true)
            ->setDescription('Home module')
        );

        $manager->persist((new ModuleParameter)
            ->setModule($module)
            ->setLabel('key')
            ->setValue(['_value' => 'value'])
        );

        $this->endTime($start);
    }
}
