<?php

namespace App\Controller\Front;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Services\Shop\CategoryService;
use App\Services\Shop\ProductService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="app_product_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/categories/{categorySlug}-{categoryId}/{productSlug}-{productId}", name="show", methods={"GET"},
     *     requirements={
     *     "categorySlug":"[a-z\d\-]+", "productSlug":"[a-z\d\-]+",
     *     "categoryId":"\d+", "productId":"\d+"}
     *     )
     * @Entity("product", expr="repository.getWithAllRelations(productId)")
     */
    public function show (Product $product, string $categorySlug, int $categoryId, string $productSlug, ProductRepository $productRepository, ProductService $productService, CategoryService $categoryService)
    {
        if ($categorySlug !== $product->getCategory()->getSlug() || $productSlug !== $product->getSlug() || $categoryId !== $product->getCategory()->getId()) {
            return $this->redirect($productService->getShowUrl($product));
        }

        $productFields = $productRepository->getProductFields($product);

        $breadcrumb = [
            ['label' => 'category', 'url' => $categoryService->getIndexUrl()],
            ['label' => $product->getCategory()->getLabel(),
             'url'   => $categoryService->getShowUrl($product->getCategory())],
            ['label' => $product->getLabel()]
        ];

        return $this->render('product/show.html.twig', compact('product', 'productFields', 'breadcrumb'));
    }
}
