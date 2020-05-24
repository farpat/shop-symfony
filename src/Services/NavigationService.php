<?php

namespace App\Services;


use App\Entity\Category;
use App\Entity\Product;
use App\Services\Shop\CategoryService;
use App\Services\Shop\ProductService;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\CacheInterface;

class NavigationService
{
    private array                 $resources = [];
    private ?string               $currentUrl;
    private CategoryService       $categoryService;
    private UrlGeneratorInterface $urlGenerator;
    private ModuleService         $moduleService;
    private ProductService        $productService;
    /**
     * @var CacheItemPoolInterface|CacheInterface
     */
    private CacheInterface $cache;

    public function __construct(
        ModuleService $moduleService,
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator,
        ProductService $productService,
        CategoryService $categoryService,
        CacheInterface $cache
    ) {
        $this->currentUrl = $requestStack->getCurrentRequest() ? $requestStack->getCurrentRequest()->getPathInfo() : null;
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->urlGenerator = $urlGenerator;
        $this->moduleService = $moduleService;
        $this->cache = $cache;
    }

    public function generateHtml(): string
    {
        $navigation = $this->moduleService->getParameter('home', 'navigation');
        $links = $navigation->getValue();

        $this->resources = $this->cache->get('navigation#resources',
            fn() => $this->getResources($links));

        $html = '';
        foreach ($links as $key => $link1) {
            $html .= is_int($key) ? $this->renderLink1($link1) : $this->renderLinks2($key, $link1);
        }
        return $html;
    }

    private function getResources(array $links)
    {
        $resources = [];

        foreach ($links as $key => $link1) {
            if (is_int($key)) {
                [$entityClass, $id] = explode(':', $link1);
                $resources[$entityClass][$id] = true;
            } else {
                [$entityClass, $id] = explode(':', $key);
                $resources[$entityClass][$id] = true;

                foreach ($link1 as $link2) {
                    [$entityClass, $id] = explode(':', $link2);
                    $resources[$entityClass][$id] = true;
                }
            }
        }

        foreach ($resources as $entityClass => $ids) {
            $ids = array_keys($ids);
            $items = [];
            switch ($entityClass) {
                case Product::class:
                    $items = $this->productService->getProductsForMenu($ids);
                    break;
                case Category::class:
                    $items = $this->categoryService->getCategoriesForMenu($ids);
                    break;
            }

            $resources[$entityClass] = $this->getById($items);
        }

        return $resources;
    }

    /**
     * @param Product[]|Category[] $array
     */
    private function getById(array $array)
    {
        $newArray = [];
        foreach ($array as $item) {
            $newArray[$item->getId()] = $item;
        }

        return $newArray;
    }

    private function renderLink1(string $link1): string
    {
        $resource = $this->getResource($link1);
        $url = $this->getUrl($resource);
        $activeClass = $url === $this->currentUrl ? ' active' : '';

        return "<li class=\"nav-item\"><a class=\"nav-link{$activeClass}\" href=\"{$url}\">{$resource->getLabel()}</a></li>";

    }

    /**
     * @param string $link1
     *
     * @return Product|Category
     * @throws Exception
     */
    private function getResource(string $link1)
    {
        [$model, $id] = explode(':', $link1);

        if (!$resource = $this->resources[$model][$id]) {
            throw new Exception("The model << $model >> (id: $id) is not found!");
        }

        return $resource;
    }

    private function getUrl($entity): string
    {
        if ($entity instanceof Product) {
            return $this->urlGenerator->generate('app_front_product_show', [
                'productId'    => $entity->getId(),
                'productSlug'  => $entity->getSlug(),
                'categoryId'   => $entity->getCategory()->getId(),
                'categorySlug' => $entity->getCategory()->getSlug()
            ]);
        }

        if ($entity instanceof Category) {
            return $this->urlGenerator->generate('app_front_category_show', [
                'categoryId'   => $entity->getId(),
                'categorySlug' => $entity->getSlug()
            ]);
        }

        return '';
    }

    private function renderLinks2(string $link, array $links): string
    {
        $resource = $this->getResource($link);

        $itemsHtml = array_reduce($links, function ($acc, $link) {
            $acc .= $this->renderLink2($link);
            return $acc;
        });

        return <<<HTML
<li class="nav-item dropdown">
    <button class="nav-link btn btn-link dropdown-toggle" id="dropdown-{$resource->getSlug()}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {$resource->getLabel()}
    </button>
    
    <div class="dropdown-menu" aria-labelledby="dropdown-{$resource->getSlug()}">
        {$itemsHtml}
    </div>
</li>
HTML;
    }

    private function renderLink2(string $link): string
    {
        $resource = $this->getResource($link);
        $url = $this->getUrl($resource);

        $activeClass = $url === $this->currentUrl ? ' active' : '';

        return <<<HTML
<a class="dropdown-item{$activeClass}" href="{$url}">{$resource->getLabel()}</a>
HTML;
    }
}