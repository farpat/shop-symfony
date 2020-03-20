<?php

namespace App\DataFixtures;

use App\Entity\{Module, Product, Category};
use App\Repository\ModuleRepository;
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

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
        $this->console = new ConsoleOutput();
    }

    private function startTime(string $label): float
    {
        $this->console->write("<comment>Start: $label\n</comment>");
        return microtime(true);
    }

    private function endTime(float $start): void
    {
        $end = microtime(true);
        $this->console->write(sprintf("<info> => Finish: %s seconds\n</info>", round($end - $start, 2)));
    }

    public function load(ObjectManager $manager)
    {
        $this->resetAndCreationOfModules($manager);

        $manager->flush();
    }

    private function resetAndCreationOfModules(ObjectManager $manager)
    {
        $start = $this->startTime('Reset and Creation of modules');
        /** @var ModuleRepository */
        $moduleRepository = $manager->getRepository(Module::class);
        
        $moduleRepository->createModule('home', true, 'Home module');
        $moduleRepository->createModule('billing', true, 'Billing module');
        //Forced to flush to add the modules because after we get it from database
        $manager->flush();

        //Home module
        $moduleRepository->createParameter('home', 'navigation', [
            Category::class . ':2' => [Product::class . ':1', Product::class . ':2', Product::class . ':3'],
            Category::class . ':5' => [Product::class . ':4', Product::class . ':6', Product::class . ':5'],
            Product::class . ':10'
        ]);
        $moduleRepository->createParameter('home', 'display', ['carousel', 'categories', 'products', 'elements']);
        $moduleRepository->createParameter('home', 'products', [1, 2]);
        $moduleRepository->createParameter('home', 'categories', [1, 2]);
        $moduleRepository->createParameter('home', 'elements', [
            ['icon' => 'fas fa-book', 'title' => 'Book 1'],
            ['icon' => 'fas fa-book', 'title' => 'Book 2'],
            ['icon' => 'fas fa-book', 'title' => 'Book 3'],
        ]);
        $moduleRepository->createParameter('home', 'carousel', [
            ['title' => 'Slide 1', 'description' => 'Slide 1', 'img' => 'https://picsum.photos/id/1/1000/400'],
            ['title' => 'Slide 2', 'description' => 'Slide 2', 'img' => 'https://picsum.photos/id/2/1000/400'],
            ['title' => 'Slide 3', 'description' => 'Slide 3', 'img' => 'https://picsum.photos/id/3/1000/400'],
        ]);

        //Billing module
        $line1 = $this->faker->streetAddress;
        $line2 = $this->faker->boolean(70) ? ucfirst($this->faker->words(3, true)) : '';
        $postalCode = $this->faker->postcode;
        $city = $this->faker->city;
        $country = $this->faker->country;
        $latitude = $this->faker->latitude;
        $longitude = $this->faker->longitude;
        $text = $line1 . ' ' . $line2 . ' ' . $postalCode . ' ' . $city . ', ' . $country;

        $moduleRepository->createParameter('billing', 'next_number', ['_value' => 1]);
        $moduleRepository->createParameter('billing', 'currency', ['style' => 'right', 'code' => 'EUR', 'symbol' => '€']);
        $moduleRepository->createParameter('billing', 'address', compact('line1', 'line2', 'postalCode', 'city', 'country', 'latitude', 'longitude', 'text'));
        $moduleRepository->createParameter('billing', 'phone_number', ['_value' => $this->faker->phoneNumber]);


        $this->endTime($start);
    }
}
