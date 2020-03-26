<?php

namespace App\Services\Shop;


use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoryService
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct (CategoryRepository $categoryRepository, UrlGeneratorInterface $urlGenerator)
    {
        $this->categoryRepository = $categoryRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function getShowUrl (Category $category)
    {
        return $this->urlGenerator->generate('category.show', [
            'categorySlug' => $category->getSlug(),
            'categoryId'   => $category->getId(),
        ]);
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
}