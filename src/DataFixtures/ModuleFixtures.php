<?php

namespace App\DataFixtures;

use App\Entity\{Category, Product, Module};
use App\Repository\ModuleRepository;
use App\Services\DataFixtures\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ModuleFixtures extends Fixture implements OrderedFixtureInterface
{

    private function createModules(ModuleRepository $moduleRepository, ObjectManager $manager)
    {
        $moduleRepository->createModule('home', true, 'Home module');
        $moduleRepository->createModule('billing', true, 'Billing module');
    }

    private function createHomeModuleParameters(ModuleRepository $moduleRepository)
    {
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
    }

    private function createBillingModuleParameters(ModuleRepository $moduleRepository)
    {
        $line1 = $this->faker->streetAddress;
        $line2 = $this->faker->boolean(70) ? ucfirst($this->faker->words(3, true)) : '';
        $postal_code = $this->faker->postcode;
        $city = $this->faker->city;
        $country = $this->faker->country;
        $latitude = $this->faker->latitude;
        $longitude = $this->faker->longitude;
        $text = $line1 . ' ' . $line2 . ' ' . $postal_code . ' ' . $city . ', ' . $country;

        $moduleRepository->createParameter('billing', 'next_number', ['_value' => 1]);
        $moduleRepository->createParameter('billing', 'currency', ['style' => 'right', 'code' => 'EUR', 'symbol' => '€']);
        $moduleRepository->createParameter('billing', 'address', compact('line1', 'line2', 'postal_code', 'city', 'country', 'latitude', 'longitude', 'text'));
        $moduleRepository->createParameter('billing', 'phone_number', ['_value' => $this->faker->phoneNumber]);
    }

    public function load(ObjectManager $manager)
    {
        $moduleRepository = $manager->getRepository(Module::class);

        $this->createModules($moduleRepository, $manager);
        //Forced to flush to add the modules because after we get it from database
        $manager->flush();

        $this->createHomeModuleParameters($moduleRepository);
        $this->createBillingModuleParameters($moduleRepository);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
