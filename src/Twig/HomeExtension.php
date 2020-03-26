<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use App\Repository\ModuleRepository;
use App\Repository\ProductRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HomeExtension extends AbstractExtension
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct (ModuleRepository $moduleRepository, CategoryRepository $categoryRepository, ProductRepository $productRepository)
    {
        $this->moduleRepository = $moduleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    public function getFunctions (): array
    {
        return [
            new TwigFunction('productsInHome', [$this, 'getProductsInHome']),
            new TwigFunction('categoriesInHome', [$this, 'getCategoriesInHome'])
        ];
    }

    public function getProductsInHome (): array
    {
        return $this->productRepository->getProductsInHome();
    }

    public function getCategoriesInHome (): array
    {
        return $this->categoryRepository->getCategoriesInHome();
    }
}
