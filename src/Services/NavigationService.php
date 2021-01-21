<?php

namespace App\Services;


use App\Entity\Category;
use App\Entity\Product;
use App\Services\Shop\CategoryService;
use App\Services\Shop\ProductService;
use App\Services\Support\Str;
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
        $links = $this->moduleService->getParameter('home', 'navigation')->getValue();

        $this->resources = $this->cache->get('navigation#resources', fn() => $this->getResources($links));

        $informations = array_map(fn($link) => $this->getInformation($link), $links);

        $html = '';

        foreach ($informations as $key => $information) {
            $nextInformation = $informations[$key + 1] ?? null;

            switch ($information['level']) {
                case 1:
                    $hasLevel3 = null;
                    if (!$nextInformation || $nextInformation['level'] === 1) {
                        $html .= $this->renderLink($information);
                    } else {
                        $html .= $this->renderStartDropdownButton($information);
                    }
                    break;
                case 2:
                    $hasLevel3 = $hasLevel3 ?? $this->hasLevel3($key + 1, $informations);
                    $html .= $this->renderDropdownItem2($information, $hasLevel3);

                    if (!$nextInformation || $nextInformation['level'] === 1) {
                        $html .= $this->renderEndDropdownButton();
                    }
                    break;
                case 3:
                    $html .= $this->renderDropdownItem3($information);

                    if (!$nextInformation || $nextInformation['level'] === 1) {
                        $html .= $this->renderEndDropdownButton();
                    }
                    break;
            }
        }

        return $html;
    }

    private function getResources(array $links)
    {
        $resources = [];

        foreach ($links as $key => $link) {
            if (preg_match('/^\d+\[ENTITY\](.*)@(.*)/', $link, $matches)) {
                [, $entityClass, $id] = $matches;
                $resources[$entityClass][$id] = true;
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

    private function getInformation(string $link): array
    {
        preg_match('/^(\d+)\[(.*)\](.*)@(.*)/', $link, $matches);
        array_shift($matches);
        return [
            'level'  => (int)$matches[0],
            'type'   => $matches[1],
            'prefix' => $matches[2],
            'suffix' => $matches[3]
        ];
    }

    private function renderLink(array $link1): string
    {
        switch ($link1['type']) {
            case 'ENTITY':
                $resource = $this->getResource($link1['prefix'], $link1['suffix']);

                $url = $this->getUrl($resource);
                $label = $resource->getLabel();
                $activeClass = $url === $this->currentUrl ? ' active' : '';
                $target = '';
                break;
            case 'LINK':
                $url = $link1['prefix'];
                $label = $link1['suffix'];
                $activeClass = '';
                $target = ' target="_blank"';
                break;
            default:
                throw new Exception("Not managed!");
        }

        return <<<HTML
<div class="nav-item"><a class="nav-link{$activeClass}" href="{$url}"{$target}>{$label}</a></div>
HTML;


    }

    /**
     * @param string $link1
     *
     * @return Product|Category
     * @throws Exception
     */
    private function getResource(string $model, string $id)
    {
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

    private function renderStartDropdownButton(array $information)
    {
        switch ($information['type']) {
            case 'TEXT':
                $label = $information['prefix'];
                $slug = Str::getSnakeCase($label);
                break;
            case 'ENTITY':
                $resource = $this->getResource($information['prefix'], $information['suffix']);
                $label = $resource->getLabel();
                $slug = $resource->getSlug();
                break;
            default:
                throw new Exception("Not managed!");
        }

        return <<<HTML
<div class="nav-item nav-dropdown">
    <button class="nav-link nav-link-dropdown" id="dropdown-{$slug}">{$label}</button>

    <div class="nav-dropdown-items" aria-labelledby="dropdown-{$slug}">
HTML;

    }

    private function hasLevel3(int $startKey, array $informations)
    {
        for ($i = $startKey; $i < count($informations); $i++) {
            if ($informations[$i]['level'] === 1) {
                break;
            }
            if ($informations[$i]['level'] === 3) {
                return true;
            }
        }

        return false;
    }

    private function renderDropdownItem2(array $information, bool $hasLevel3)
    {
        switch ($information['type']) {
            case 'ENTITY':
                $resource = $this->getResource($information['prefix'], $information['suffix']);

                $url = $this->getUrl($resource);
                $label = $resource->getLabel();
                $activeClass = $url === $this->currentUrl ? ' active' : '';
                $target = '';
                break;
            case 'LINK':
                $url = $information['prefix'];
                $label = $information['suffix'];
                $activeClass = '';
                $target = 'target="_blank"';
                break;
        }

        if ($hasLevel3) {
            return <<<HTML
<h2 class="nav-dropdown-item nav-dropdown-item-title"><a class="nav-link{$activeClass}" {$target} href="{$url}">{$label}</a></h2>
HTML;
        } else {
            return <<<HTML
<div class="nav-dropdown-item"><a class="nav-link{$activeClass}" {$target} href="{$url}">{$label}</a></div>
HTML;
        }
    }

    private function renderEndDropdownButton()
    {
        return "</div></div>";
    }

    private function renderDropdownItem3(array $information)
    {
        switch ($information['type']) {
            case 'ENTITY':
                $resource = $this->getResource($information['prefix'], $information['suffix']);
                $url = $this->getUrl($resource);
                $label = $resource->getLabel();
                $activeClass = $url === $this->currentUrl ? ' active' : '';
                $target = '';
                break;
            case 'LINK':
                $url = $information['prefix'];
                $label = $information['suffix'];
                $activeClass = '';
                $target = ' target="_blank"';
                break;
        }

        return <<<HTML
<div class="nav-dropdown-item"><a class="nav-link{$activeClass}"{$target} href="{$url}">{$label}</a></div>
HTML;
    }
}
