<?php

namespace App\DataFixtures;

use App\Services\DataFixtures\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\{Category, Image, Product, ProductField, ProductReference, Tax};
use App\Repository\CategoryRepository;

class ProductFieldAndProductReferenceFixtures extends Fixture implements OrderedFixtureInterface
{
    private const STRING_PRODUCT_FIELDS = [
        'color'    => ['white', 'red', 'green', 'blue', 'yellow', 'orange'],
        'size'     => ['s', 'm', 'l', 'xs', 'xl', 'xxl'],
        'material' => ['wood', 'plastic', 'metal'],
        'form'     => ['square', 'rectangle', 'round', 'diamond'],
    ];

    private const NUMBER_PRODUCT_FIELDS = [
        'storage space' => [8, 16, 32, 64, 128, 256],
        'weight in kg'  => [1, 4, 8, 16],
        'height in cm'  => [4, 16, 32, 64],
        'width in cm'   => [4, 16, 32, 64],
    ];

    public function load(ObjectManager $manager)
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $manager->getRepository(Category::class);
        $subCategories = $categoryRepository
            ->createQueryBuilder('c')
            ->leftJoin('c.products', 'p')
            ->where('c.is_last = 1')
            ->getQuery()
            ->getResult();

        foreach ($subCategories as $subCategory) {
            $productFields = $this->makeProductFields($subCategory, $manager);
            //Forced to flush because we need product field ids to create consistent product references
            $manager->flush();

            foreach ($subCategory->getProducts() as $product) {
                $productReferences = $this->makeProductReferences($product, $productFields, $manager);

                $this->attachImages($product, $productReferences, $manager);
            }
        }

        $manager->flush();
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

    /**
     * @param Product $product
     * @return float[]
     */
    private function computePricesOfProduct(Product $product)
    {
        $unitPriceExcludingTaxes = pow(10, random_int(1, 5));

        $totalTaxes = array_reduce($product->getTaxes()->toArray(), function ($acc, Tax $tax) use ($unitPriceExcludingTaxes) {
            if ($tax->getType() === Tax::UNITY_TYPE) {
                $acc += $tax->getValue();
            } elseif ($tax->getType() === Tax::PERCENTAGE_TYPE) {
                $acc += $unitPriceExcludingTaxes * ($tax->getValue() / 100);
            }

            return $acc;
        }, 0);

        $unitPriceIncludingTaxes = $totalTaxes + $unitPriceExcludingTaxes;

        return [$unitPriceExcludingTaxes, $unitPriceIncludingTaxes];
    }


    /**
     * @return ProductField[]
     */
    private function makeProductFields(Category $category, ObjectManager $manager): array
    {
        $numbers = self::NUMBER_PRODUCT_FIELDS;
        $strings = self::STRING_PRODUCT_FIELDS;

        $count = random_int(0, 4);

        if ($count === 0) {
            return [];
        }

        $productFields = [];

        for ($i = 0; $i < $count; $i++) {
            $type = $this->faker->boolean ? 'string' : 'number';
            $label = array_rand(${$type . 's'});
            unset(${$type . 's'}[$label]);

            $productField = (new ProductField)
                ->setCategory($category)
                ->setType($type)
                ->setLabel($label)
                ->setIsRequired(true);

            $manager->persist($productField);

            $productFields[] = $productField;
        }

        return $productFields;
    }

    /**
     * @param Product $product
     * @param ProductField[] $productFields
     * @param Tax[] $taxes
     * 
     * @return ProductReference[]
     */
    private function makeProductReferences(Product $product, array $productFields, ObjectManager $manager): array
    {
        [$unitPriceExcludingTaxes, $unitPriceIncludingTaxes] = $this->computePricesOfProduct($product);

        $productReferences = [];
        $productReferencesCount = random_int(1, 3);

        for ($i = 0; $i < $productReferencesCount; $i++) {
            $filledProductfields = [];
            
            if (!empty($productFields)) {
                foreach ($productFields as $productField) {
                    $values = $productField->getType() === 'string' ?
                        self::STRING_PRODUCT_FIELDS[$productField->getLabel()] :
                        self::NUMBER_PRODUCT_FIELDS[$productField->getLabel()];

                    $value = $values[array_rand($values)];

                    $filledProductfields[$productField->getId()] = $value;
                    $labelsArray[] = $productField->getLabel() . ' - ' . $value;
                }
            }

            $productReference = (new ProductReference)
                ->setLabel($product->getLabel())
                ->setProduct($product)
                ->setUnitPriceExcludingTaxes($unitPriceExcludingTaxes)
                ->setUnitPriceIncludingTaxes($unitPriceIncludingTaxes)
                ->setFilledProductFields($filledProductfields);


            $manager->persist($productReference);

            $productReferences[] = $productReference;
        }

        return $productReferences;
    }

    /**
     * @param Product $product
     * @param ProductReference[] $productReferences
     * @param ObjectManager $manager
     */
    private function attachImages(Product $product, array $productReferences, ObjectManager $manager)
    {
        $imagesCount = random_int(0, 5);
        if ($imagesCount === 0) {
            return;
        }

        foreach ($productReferences as $productReference) {
            $images = [];
            for ($i = 0; $i < $imagesCount; $i++) {
                $image = $this->makeImage($manager);
                $images[] = $image;
                $productReference->addImage($image);
            }

            $productReference->setMainImage($images[0]);
        }

        $product->setMainImage($images[0]);
    }

    public function getOrder()
    {
        return 3;
    }
}
