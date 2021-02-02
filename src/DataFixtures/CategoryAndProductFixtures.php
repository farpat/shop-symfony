<?php

namespace App\DataFixtures;

use App\Entity\{Category, Product, Tax};
use App\Services\DataFixtures\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

class CategoryAndProductFixtures extends Fixture implements OrderedFixtureInterface
{
    protected ?ObjectManager $entityManager = null;

    public function load(ObjectManager $manager)
    {
        $this->entityManager = $manager;

        $categoriesCount = random_int(10, 15);
        [$vatTax, $ecoTax] = $this->makeTaxes();

        for ($i = 0; $i < $categoriesCount; $i++) {
            $category = $this->makeCategory();

            foreach ($this->makeSubCategories($category) as $subCategory) {
                foreach ($this->makeProducts($subCategory) as $product) {
                    $this->attachTaxes($product, $vatTax, $ecoTax);
                }
            }

        }
        $manager->flush();
    }

    /**
     * @return Tax[]
     */
    private function makeTaxes(): array
    {
        $vatTax = (new Tax)->setLabel('VAT tax')->setType('PERCENTAGE')->setValue(20);
        $ecoTax = (new Tax)->setLabel('Eco tax')->setType('UNITY')->setValue(0.05);

        $this->entityManager->persist($vatTax);
        $this->entityManager->persist($ecoTax);

        return [$vatTax, $ecoTax];
    }

    private function makeCategory(): Category
    {
        $label = $this->faker->unique()->word;
        $slug = $this->slugify($label);

        $category = (new Category)
            ->setLabel($label)
            ->setNomenclature(strtoupper($label))
            ->setSlug($slug)
            ->setDescription($this->faker->words(8, true))
            ->setImage($image = $this->makeImage());


        $this->entityManager->persist($image);
        $this->entityManager->persist($category);

        return $category;
    }

    private function slugify(string $string): string
    {
        return (new AsciiSlugger())->slug(strtolower($string));
    }

    private function makeSubCategories(Category $parentCategory): array
    {
        $subCategories = [];

        for ($i = 0; $i < 2; $i++) {
            $label = $parentCategory->getLabel() . ' ' . $this->faker->unique()->word;
            $slug = $this->slugify($label);
            $nomenclature = $parentCategory->getNomenclature() . '.' . str_replace('-', ' ', strtoupper($slug));

            $subCategory = (new Category)
                ->setLabel($label)
                ->setSlug($slug)
                ->setNomenclature($nomenclature)
                ->setDescription($this->faker->words(5, true))
                ->setImage($image = $this->makeImage());

            $this->entityManager->persist($image);
            $this->entityManager->persist($subCategory);
            $subCategories[] = $subCategory;
        }

        return $subCategories;
    }

    /**
     * @return Product[]
     */
    private function makeProducts(Category $category): array
    {
        $productsCount = random_int(10, 20);
        $products = [];

        for ($i = 0; $i < $productsCount; $i++) {
            $label = ucfirst($this->faker->unique()->words(3, true));
            $slug = $this->slugify($label);
            $excerpt = $this->faker->boolean(75) ? $this->faker->sentence(7) : null;
            $description = $excerpt ?
                array_reduce($this->faker->paragraphs(5), function (string $carry, string $paragraph) {
                    $carry .= '<p>' . $paragraph . '</p>';
                    return $carry;
                }, '') : null;

            $product = (new Product)
                ->setLabel($label)
                ->setSlug($slug)
                ->setExcerpt($excerpt)
                ->setDescription($description)
                ->setCategory($category);

            $this->entityManager->persist($product);

            $products[] = $product;
        }

        return $products;
    }

    private function attachTaxes(Product $product, Tax $vatTax, Tax $ecoTax)
    {
        $product->addTax($vatTax);

        if ($this->faker->boolean(25)) {
            $product->addTax($ecoTax);
        }
    }

    public function getOrder()
    {
        return 2;
    }
}
