<?php

namespace App\DataFixtures;

use App\Entity\{Category, Product, Module, Tax};
use App\Repository\ModuleRepository;
use App\Services\DataFixtures\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Helper\ProgressIndicator;

class CategoryFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $categoriesCount = random_int(10, 15);
        $progress = new ProgressIndicator($this->console, $categoriesCount);
        [$vatTax, $ecoTax] = $this->makeTaxes($manager);

        for ($i = 0; $i < $categoriesCount; $i++) {
            $category = $this->makeCategory($manager);
        }

        $manager->flush();
    }

    private function makeCategory(ObjectManager $manager): Category
    {
        $label = $this->faker->word;
        $slug = strtolower($label);


        $category = (new Category)
            ->setLabel($label)
            ->setSlug($slug)
            ->setDescription($this->faker->paragraphs(3, true))
            ->setIsLast(false);

        //TODO: Image should be nullable...


        $manager->persist($category);

        return $category;
    }


    /**
     * @return Tax[]
     */
    private function makeTaxes(ObjectManager $manager): array
    {
        $vatTax = (new Tax)->setLabel('VAT tax')->setType('PERCENTAGE')->setValue(20);
        $ecoTax = (new Tax)->setLabel('Eco tax')->setType('UNITY')->setValue(0.05);

        $manager->persist($vatTax);
        $manager->persist($ecoTax);

        return [$vatTax, $ecoTax];
    }
}
