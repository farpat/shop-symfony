<?php

namespace App\Services\Shop;


use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Services\CacheWrapper;
use App\Services\ModuleService;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CategoryService
{
    private CategoryRepository    $categoryRepository;
    private UrlGeneratorInterface $urlGenerator;
    private ModuleService         $moduleService;
    /**
     * @var CacheInterface|CacheItemPoolInterface
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

    public function getCategoriesForMenu(array $ids): array
    {
        return $this->categoryRepository->getCategoriesForMenu($ids);
    }

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

    public function getRootCategories(): array
    {
        return $this->cache->get('category#getRootCategories', function (ItemInterface $item) {
            return $this->categoryRepository->getRootCategories();
        });
    }

    public function getProducts(Category $category): array
    {
        return $this->categoryRepository->getProducts($category);
    }

    /**
     * @param Category[] $parentCategories
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function generateHtml(array $parentCategories, bool $isRootCall = true): string
    {
        $cacheKey = 'category#generateHtml';
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($isRootCall && $this->cache->hasItem($cacheKey)) {
            return $cacheItem->get();
        }

        if (empty($parentCategories)) {
            return '';
        }

        $string = '';
        foreach ($parentCategories as $parentCategory) {
            $sourceAttribute = $parentCategory->getImage() ? $parentCategory->getImage()->getUrlThumbnail() : 'https://via.placeholder.com/80x32';
            $altAttribute = $parentCategory->getImage() ? $parentCategory->getImage()->getAltThumbnail() : $parentCategory->getLabel();
            $imageElement = "<img src='$sourceAttribute' alt='$altAttribute'>";

            $children = $this->categoryRepository->getChildren($parentCategory);
            $string .= <<<HTML
                <div class="media">
                    <a href="{$this->getShowUrl($parentCategory)}" class="media-link">
                        $imageElement
                    </a>
                    <div class="media-body">
                        <h2 class="media-title"><a href="{$this->getShowUrl($parentCategory)}">{$parentCategory->getLabel()}</a></h2>
                        {$this->generateHtml($children, false)}
                    </div>
                </div>
                HTML;
        }

        if ($isRootCall) {
            $this->cache->save($cacheItem->set($string));
        }

        return $string;
    }

    private function getShowUrl(Category $category)
    {
        return $this->urlGenerator->generate('app_front_category_show', [
            'categorySlug' => $category->getSlug(),
            'categoryId'   => $category->getId(),
        ]);
    }
}