<?php

namespace App\Services\Shop;


use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Services\ModuleService;
use Symfony\Component\Cache\Adapter\TraceableAdapter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CategoryService
{
    private CategoryRepository    $categoryRepository;
    private UrlGeneratorInterface $urlGenerator;
    private ModuleService         $moduleService;
    /**
     * @var TraceableAdapter|CacheInterface
     */
    private CacheInterface $cache;

    public function __construct(
        CategoryRepository $categoryRepository,
        UrlGeneratorInterface $urlGenerator,
        ModuleService $moduleService,
        CacheInterface $cache
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->urlGenerator = $urlGenerator;
        $this->moduleService = $moduleService;
        $this->cache = $cache;
    }

    /**
     * @param int[] $ids
     * @return Category[]
     */
    public function getCategoriesForMenu(array $ids): array
    {
        return $this->categoryRepository->getCategoriesForMenu($ids);
    }

    /**
     * @return Category[]
     */
    public function getCategoriesInHome(): array
    {
        return $this->cache->get('category#getCategoriesInHome', function (ItemInterface $item) {
            $categoryIds = $this->moduleService->getParameter('home', 'categories')->getValue();
            if (empty($categoryIds)) {
                return [];
            }
            return $this->categoryRepository->getCategoriesInHome($categoryIds);
        });
    }

    /**
     * @return Category[]
     */
    public function getRootCategories(): array
    {
        return $this->categoryRepository->getRootCategories();
    }

    /**
     * @param Category $category
     * @return Product[]
     */
    public function getProducts(Category $category): array
    {
        return $this->categoryRepository->getProducts($category);
    }

    /**
     * @param Category[] $parentCategories
     */
    public function generateListForCategoryIndexAdmin(
        array $parentCategories,
        bool $mustDeleteCache = false,
        bool $isRootCall = true
    ): array {
        $getCategories = function (array $parentCategories) {
            $array = [];

            if (empty($parentCategories)) {
                return $array;
            }

            foreach ($parentCategories as $parentCategory) {
                $children = $this->categoryRepository->getChildren($parentCategory);

                $array[] = [
                    'category' => $parentCategory,
                    'children' => $this->generateListForCategoryIndexAdmin($children)
                ];
            }

            return $array;
        };

        if ($isRootCall) {
            $cacheKey = 'category#generateListForCategoryIndexAdmin';
            if ($mustDeleteCache) {
                $this->cache->delete($cacheKey);
            }
            return $this->cache->get($cacheKey, fn() => $getCategories($parentCategories));
        } else {
            return $getCategories($parentCategories);
        }
    }

    /**
     * @param Category[] $parentCategories
     */
    public function generateHtmlForCategoryIndex(array $parentCategories, bool $isRootCall = true): string
    {
        $getHtml = function ($parentCategories) {
            $string = '';
            foreach ($parentCategories as $parentCategory) {
                $sourceAttribute = $parentCategory->getImage() ? $parentCategory->getImage()->getUrlThumbnail() : 'https://via.placeholder.com/80x32';
                $altAttribute = $parentCategory->getImage() ? $parentCategory->getImage()->getAltThumbnail() : $parentCategory->getLabel();
                $imageElement = "<img src='$sourceAttribute' alt='$altAttribute'>";

                $children = $this->categoryRepository->getChildren($parentCategory);
                $string .= <<<HTML
                <div class="media">
                    <a href="{$this->getShowUrl($parentCategory)}" class="media-link">$imageElement</a>
                    <div class="media-body">
                        <h2 class="media-title"><a href="{$this->getShowUrl($parentCategory)}">{$parentCategory->getLabel()}</a></h2>
                        {$this->generateHtmlForCategoryIndex($children, false)}
                    </div>
                </div>
                HTML;
            }

            return $string;
        };

        if ($isRootCall) {
            $cacheKey = 'category#generateHtmlForCategoryIndex';
            return $this->cache->get($cacheKey, fn() => $getHtml($parentCategories));
        } else {
            return $getHtml($parentCategories);
        }


    }

    private function getShowUrl(Category $category): string
    {
        return $this->urlGenerator->generate('app_front_category_show', [
            'categorySlug' => $category->getSlug(),
            'categoryId'   => $category->getId(),
        ]);
    }
}