<?php

namespace App\DataFixtures;

use App\Entity\{Category, Product};
use App\Services\DataFixtures\Fixture;
use App\Services\ModuleService;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ModuleFixtures extends Fixture implements OrderedFixtureInterface
{
    protected ?ObjectManager $entityManager = null;
    private ?ModuleService   $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        parent::__construct();
        $this->moduleService = $moduleService;
    }


    public function load(ObjectManager $manager)
    {
        $this->createModules();
        //Forced to flush to add the modules because after we get it from database
        $manager->flush();

        $this->createHomeModuleParameters();
        $this->createBillingModuleParameters();

        $manager->flush();
    }

    private function createModules()
    {
        $this->moduleService->createModule('home', true, 'Home module');
        $this->moduleService->createModule('billing', true, 'Billing module');
    }

    private function createHomeModuleParameters()
    {
        $this->moduleService->createParameter('home', 'navigation', [
            '1[TEXT]SubMenu 1@',
            '2[ENTITY]' . Product::class . '@1',
            '2[ENTITY]' . Product::class . '@2',
            '2[LINK]https://qwant.com@Qwant',
            '1[TEXT]SubMenu 2@',
            '2[ENTITY]' . Product::class . '@4',
            '3[ENTITY]' . Product::class . '@5',
            '3[ENTITY]' . Product::class . '@6',
            '1[ENTITY]' . Product::class . '@10',
            '1[LINK]https://github.com@Github',
        ]);
        $this->moduleService->createParameter('home', 'display', ['carousel', 'categories', 'products', 'elements']);
        $this->moduleService->createParameter('home', 'products', [1, 2]);
        $this->moduleService->createParameter('home', 'categories', [1, 2]);
        $this->moduleService->createParameter('home', 'elements', [
            [
                'icon'      => 'fas fa-phone',
                'title'     => 'After-sales service',
                'paragraph' => $this->faker->paragraph
            ],
            ['icon' => 'fas fa-truck', 'title' => 'Quick delivery', 'paragraph' => $this->faker->paragraph],
            [
                'icon'      => 'fas fa-hand-holding-usd',
                'title'     => 'Our prices are honest',
                'paragraph' => $this->faker->paragraph
            ],
        ]);
        //<div>
        //    </div>
        //    <div>
        //    </div>
        //    <div>
        //    </div>
        $this->moduleService->createParameter('home', 'carousel', [
            [
                'title'       => 'Slide 1',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. A alias autem, eos, eveniet fuga iste magni maxime minima molestias mollitia neque nisi provident quidem, recusandae repellendus sapiente ut velit veniam.',
                'img'         => 'https://picsum.photos/id/1/1000/400?blur=3'
            ],
            [
                'title'       => 'Slide 2',
                'description' => 'Consequatur ea eius eveniet excepturi illum laboriosam non officia praesentium vel, vero. Labore laudantium nisi omnis provident quibusdam? Atque consectetur consequuntur deleniti eum fugiat minus nostrum odit quibusdam quo temporibus.',
                'img'         => 'https://picsum.photos/id/2/1000/400?blur=3'
            ],
            [
                'title'       => 'Slide 3',
                'description' => 'Accusantium alias animi asperiores aspernatur commodi corporis culpa cum deleniti distinctio dolores error incidunt ipsam, iure magnam minus nemo, praesentium quasi soluta suscipit unde. Aspernatur dolorum minus modineque sapiente!',
                'img'         => 'https://picsum.photos/id/3/1000/400?blur=3'
            ],
        ]);
    }

    private function createBillingModuleParameters()
    {
        $line1 = $this->faker->streetAddress;
        $line2 = $this->faker->boolean(70) ? ucfirst($this->faker->words(3, true)) : '';
        $postal_code = $this->faker->postcode;
        $city = $this->faker->city;
        $country = $this->faker->country;
        $latitude = $this->faker->latitude;
        $longitude = $this->faker->longitude;
        $text = $line1 . ' ' . $line2 . ' ' . $postal_code . ' ' . $city . ', ' . $country;

        $this->moduleService->createParameter('billing', 'last_number', ['_value' => 1]);
        $this->moduleService->createParameter('billing', 'currency', [
            'style'  => 'right',
            'code'   => 'EUR',
            'symbol' => '€'
        ]);
        $this->moduleService->createParameter('billing', 'address',
            compact('line1', 'line2', 'postal_code', 'city', 'country', 'latitude', 'longitude', 'text'));
        $this->moduleService->createParameter('billing', 'phone_number', ['_value' => $this->faker->phoneNumber]);
    }

    public function getOrder()
    {
        return 1;
    }
}
