<?php

namespace App\Services\Shop;


use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Serializer\Normalizer\ProductNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class CategoryService
{
    private CategoryRepository $categoryRepository;
    private UrlGeneratorInterface $urlGenerator;
    private Serializer $productFieldSerializer;

    public function __construct (CategoryRepository $categoryRepository, UrlGeneratorInterface $urlGenerator, ProductService $productService)
    {
        $this->categoryRepository = $categoryRepository;
        $this->urlGenerator = $urlGenerator;
        $this->productFieldSerializer = new Serializer([new GetSetMethodNormalizer()], [new JsonEncode(['json_encode_options' => JSON_PRETTY_PRINT])]);
        $this->productSerializer = new Serializer([new ProductNormalizer($productService)], [new JsonEncode(['json_encode_options' => JSON_PRETTY_PRINT])]);
    }

    public function getProductFieldsSerialized (Category $category): ?string
    {
        return $category->getProductFields()->isEmpty() ?
            null :
            $this->productFieldSerializer->serialize($category->getProductFields(), 'json', [
                AbstractNormalizer::ATTRIBUTES => ['id', 'type', 'label']
            ]);
    }

    public function getProductsSerialized (Category $category): ?string
    {
        return empty($products = $this->categoryRepository->getProducts($category)) ?
            null :
            $this->productSerializer->serialize($products, 'json');
    }

    public function getIndexUrl ()
    {
        return $this->urlGenerator->generate('category.index');
    }

    public function generateHtml (array $parentCategories): string
    {
        $string = '';
        if (!empty($parentCategories)) {
            foreach ($parentCategories as $parentCategory) {
                $sourceAttribute = $parentCategory->getImage() ? $parentCategory->getImage()->getUrl() : 'https://via.placeholder.com/80x32';
                $altAttribute = $parentCategory->getImage() ? $parentCategory->getImage()->getAltThumbnail() : $parentCategory->getLabel();
                $imageElement = "<img src='$sourceAttribute' alt='$altAttribute'>";

                $children = $this->categoryRepository->getChildren($parentCategory);
                $string .= <<<HTML
                <div class="media">
                    <a href="{$this->getShowUrl($parentCategory)}" class="media-link">
                        $imageElement
                    </a>
                    <div class="media-body">
                        <h2><a href="{$this->getShowUrl($parentCategory)}">{$parentCategory->getLabel()}</a></h2>
                        {$this->generateHtml($children)}
                    </div>
                </div>
                HTML;

            }
        }

        return $string;
    }

    public function getShowUrl (Category $category)
    {
        return $this->urlGenerator->generate('category.show', [
            'categorySlug' => $category->getSlug(),
            'categoryId'   => $category->getId(),
        ]);
    }
}