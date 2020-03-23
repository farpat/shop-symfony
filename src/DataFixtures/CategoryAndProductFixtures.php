<?php

namespace App\DataFixtures;

use App\Entity\{Category, Image, Product, Tax};
use App\Services\DataFixtures\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryAndProductFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $categoriesCount = random_int(10, 15);
        [$vatTax, $ecoTax] = $this->makeTaxes($manager);

        for ($i = 0; $i < $categoriesCount; $i++) {
            $category = $this->makeCategory($manager);

            foreach ($this->makeSubCategories($category, $manager) as $subCategory) {
                foreach ($this->makeProducts($subCategory, $manager) as $product) {
                    $this->attachTaxes($product, $vatTax, $ecoTax);
                }
            }

        }
        $manager->flush();
    }

    /**
     * @return Tax[]
     */
    private function attachTaxes(Product $product, Tax $vatTax, Tax $ecoTax)
    {
        $product->addTax($vatTax);

        if ($this->faker->boolean(25)) {
            $product->addTax($ecoTax);
        }
    }

    /**
     * @return Product[]
     */
    private function makeProducts(Category $category, ObjectManager $manager): array
    {
        $productsCount = random_int(10, 20);
        $products = [];

        for ($i = 0; $i < $productsCount; $i++) {
            $label = ucfirst($this->faker->unique()->words(3, true));
            $slug = $this->slugify($label);
            $excerpt = $this->faker->boolean(75) ? $this->faker->sentence(7) : null;
            $description = $excerpt ? $this->faker->paragraphs(5, true) : null;

            $product = (new Product)
                ->setLabel($label)
                ->setSlug($slug)
                ->setExcerpt($excerpt)
                ->setDescription($description)
                ->setCategory($category);

            $manager->persist($product);

            $products[] = $product;
        }

        return $products;
    }

    private function makeSubCategories(Category $parentCategory, ObjectManager $manager): array
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
                ->setDescription($this->faker->paragraphs(3, true))
                ->setIsLast(true);

            $manager->persist($subCategory);
            $subCategories[] = $subCategory;
        }

        return $subCategories;
    }

    private function makeImage(ObjectManager $manager): Image
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

        $manager->persist($image);

        return $image;
    }

    private function slugify(string $string): string
    {
        return str_replace(' ', '-', strtolower($string));
    }

    private function makeCategory(ObjectManager $manager): Category
    {
        $label = $this->faker->word;
        $slug = $this->slugify($label);


        $category = (new Category)
            ->setLabel($label)
            ->setNomenclature(strtoupper($label))
            ->setSlug($slug)
            ->setDescription($this->faker->paragraphs(3, true))
            ->setIsLast(false)
            ->setImage($this->faker->boolean(75) ? $this->makeImage($manager) : null);


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

    public function getOrder()
    {
        return 2;
    }
}
