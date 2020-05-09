<?php

namespace App\DataFixtures;

use App\Entity\{Category, Product, ProductField, ProductReference, Tax};
use App\Repository\CategoryRepository;
use App\Services\DataFixtures\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

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

    protected ?ObjectManager $entityManager = null;

    public function load (ObjectManager $manager)
    {
        $this->entityManager = $manager;

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $manager->getRepository(Category::class);
        /** @var Category[] $categoriesFirstLevel */
        $categoriesFirstLevel = $categoryRepository
            ->createQueryBuilder('c')
            ->select('c', 'products')
            ->leftJoin('c.products', 'products')
            ->where('c.isLast = 0')
            ->getQuery()
            ->getResult();

        foreach ($categoriesFirstLevel as $category) {
            /** @var Category[] $subCategories */
            $subCategories = $categoryRepository
                ->createQueryBuilder('c')
                ->select('c')
                ->where('c.nomenclature LIKE :nomenclature')
                ->setParameter('nomenclature', $category->getNomenclature() . '.%')
                ->getQuery()
                ->getResult();

            $productFields = $this->makeProductFields($category, $subCategories);
            //Forced to flush because we need product field ids to create consistent product references
            $manager->flush();

            foreach ($subCategories as $subCategory) {
                foreach ($subCategory->getProducts() as $product) {
                    $productReferences = $this->makeProductReferences($product, $productFields);
                    $this->attachImages($product, $productReferences);
                }
            }
        }

        $manager->flush();
    }

    /**
     * @param Category $category
     * @param Category[] $subCategories
     *
     * @return ProductField[]
     * @throws \Exception
     */
    private function makeProductFields (Category $category, $subCategories): array
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
                ->setType($type)
                ->setLabel($label)
                ->setIsRequired(true);

            $category->addProductField($productField);
            foreach ($subCategories as $subCategory) {
                $subCategory->addProductField($productField);
            }

            $this->entityManager->persist($productField);

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
    private function makeProductReferences (Product $product, array $productFields): array
    {
        [$unitPriceExcludingTaxes, $unitPriceIncludingTaxes] = $this->computePricesOfProduct($product);

        $productReferences = [];
        $productReferencesCount = random_int(1, 3);

        for ($i = 0; $i < $productReferencesCount; $i++) {
            $filledProductfields = [];
            $labelsArray = [];

            if (!empty($productFields)) {
                foreach ($productFields as $productField) {
                    $values = $productField->getType() === 'string' ?
                        self::STRING_PRODUCT_FIELDS[$productField->getLabel()] :
                        self::NUMBER_PRODUCT_FIELDS[$productField->getLabel()];

                    $value = $values[array_rand($values)];

                    $filledProductfields[$productField->getId()] = $value;
                    $labelsArray[] = $productField->getLabel() . ': ' . $value;
                }
            }

            $productReference = (new ProductReference)
                ->setLabel($product->getLabel() . (!empty($labelsArray) ? ' => ' . implode(' | ', $labelsArray) : ''))
                ->setProduct($product)
                ->setUnitPriceExcludingTaxes($unitPriceExcludingTaxes)
                ->setUnitPriceIncludingTaxes($unitPriceIncludingTaxes)
                ->setFilledProductFields($filledProductfields);


            $this->entityManager->persist($productReference);

            $productReferences[] = $productReference;
        }

        return $productReferences;
    }

    /**
     * @param Product $product
     *
     * @return float[]
     */
    private function computePricesOfProduct (Product $product)
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
     * @param Product $product
     * @param ProductReference[] $productReferences
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    private function attachImages (Product $product, array $productReferences)
    {
        $imagesCount = random_int(0, 5);
        if ($imagesCount === 0) {
            return;
        }

        $mainImage = null;

        foreach ($productReferences as $productReference) {
            for ($i = 0; $i < $imagesCount; $i++) {
                $image = $this->makeImage();
                if ($mainImage === null) {
                    $mainImage = $image;
                }
                $productReference->addImage($image);
            }

            $productReference->setMainImage($mainImage);
        }

        $product->setMainImage($mainImage);
    }

    public function getOrder ()
    {
        return 3;
    }
}
