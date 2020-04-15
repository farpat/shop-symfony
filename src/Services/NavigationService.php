<?php

namespace App\Services;


use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ModuleRepository;
use App\Repository\ProductRepository;
use App\Services\Shop\CategoryService;
use App\Services\Shop\ProductService;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NavigationService
{
    /**
     * @var array
     */
    private $resources = [];

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;
    /**
     * @var string
     */
    private $currentUrl;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var CategoryService
     */
    private $categoryService;
    /**
     * @var ProductService
     */
    private $productService;
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    public function __construct (
        ModuleRepository $moduleRepository,
        RequestStack $request,
        UrlGeneratorInterface $urlGenerator,
        ProductRepository $productRepository, ProductService $productService,
        CategoryRepository $categoryRepository, CategoryService $categoryService)
    {
        $this->moduleRepository = $moduleRepository;
        $this->currentUrl = $request->getCurrentRequest() ? $request->getCurrentRequest()->getPathInfo() : null;
        $this->productRepository = $productRepository;
        $this->productService = $productService;
        $this->categoryRepository = $categoryRepository;
        $this->categoryService = $categoryService;
        $this->urlGenerator = $urlGenerator;
    }

    public function generateHtml (): string
    {
        if (($navigation = $this->moduleRepository->getParameter('home', 'navigation')) === null) {
            return '';
        }

        $this->setResources($links = $navigation->getValue());

        $html = '';

        foreach ($links as $key => $link1) {
            $html = is_int($key) ?
                $html . $this->renderLink1($link1) :
                $html . $this->renderLinks2($key, $link1);
        }

        return $html;
    }

    private function setResources (array $links)
    {
        $resources = [];

        foreach ($links as $key => $link1) {
            if (is_int($key)) {
                [$model, $id] = explode(':', $link1);
                $resources[$model][$id] = true;
            } else {
                [$model, $id] = explode(':', $key);
                $resources[$model][$id] = true;

                foreach ($link1 as $link2) {
                    [$model, $id] = explode(':', $link2);
                    $resources[$model][$id] = true;
                }
            }
        }

        foreach ($resources as $model => $ids) {
            $ids = array_keys($ids);
            $items = [];
            switch ($model) {
                case Product::class:
                    $items = $this->productRepository->createQueryBuilder('p')->where('p.id IN (:ids)')->setParameter('ids', $ids)->getQuery()->getResult();
                    break;
                case Category::class:
                    $items = $this->categoryRepository->createQueryBuilder('c')->where('c.id IN (:ids)')->setParameter('ids', $ids)->getQuery()->getResult();
                    break;
            }

            $resources[$model] = $this->getById($items);
        }

        $this->resources = $resources;
    }

    /**
     * @param Product[]|Category[] $array
     */
    private function getById (array $array)
    {
        $newArray = [];
        foreach ($array as $item) {
            $newArray[$item->getId()] = $item;
        }

        return $newArray;
    }

    private
    function renderLink1 (string $link1): string
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
    private
    function getResource (string $link1)
    {
        [$model, $id] = explode(':', $link1);

        if (!$resource = $this->resources[$model][$id]) {
            throw new Exception("The model << $model >> (id: $id) is not found!");
        }

        return $resource;
    }

    private function getUrl ($entity): string
    {
        if ($entity instanceof Product) {
            return $this->urlGenerator->generate('app_product_show', [
                'productId'    => $entity->getId(),
                'productSlug'  => $entity->getSlug(),
                'categoryId'   => $entity->getCategory()->getId(),
                'categorySlug' => $entity->getCategory()->getSlug()
            ]);
        }

        if ($entity instanceof Category) {
            return $this->urlGenerator->generate('app_category_show', [
                'categoryId'   => $entity->getId(),
                'categorySlug' => $entity->getSlug()
            ]);
        }

        return '';
    }

    private
    function renderLinks2 (string $link, array $links): string
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

    private
    function renderLink2 (string $link): string
    {
        $resource = $this->getResource($link);
        $url = $this->getUrl($resource);

        $activeClass = $url === $this->currentUrl ? ' active' : '';

        return <<<HTML
<a class="dropdown-item{$activeClass}" href="{$url}">{$resource->getLabel()}</a>
HTML;
    }
}