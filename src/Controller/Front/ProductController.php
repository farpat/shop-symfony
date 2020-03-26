<?php

namespace App\Controller\Front;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="product.")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/categories/{categorySlug}-{categoryId}/{productSlug}-{productId}", name="show", methods={"GET"})
     * @Entity("product", expr="repository.getWithAllRelations(productId)")
     */
    public function show (Product $product, string $categorySlug, int $categoryId, string $productSlug, ProductRepository $productRepository)
    {
        if ($categorySlug !== $product->getCategory()->getSlug() || $productSlug !== $product->getSlug() || $categoryId !== $product->getCategory()->getId()) {
            return $this->redirectToRoute('product.show', [
                'categorySlug' => $product->getCategory()->getSlug(),
                'categoryId'   => $product->getCategory()->getId(),
                'productSlug'  => $product->getSlug(),
                'product'      => $product->getId()
            ]);
        }

        $productFields = $productRepository->getProductFields($product);

        $breadcrumb = [
            ['label' => 'category', 'url' => $this->generateUrl('category.index')],
            ['label' => $product->getCategory()->getLabel(),
             'url'   => $this->generateUrl('category.show', ['categorySlug' => $product->getCategory()->getSlug(),
                                                             'category'     => $product->getCategory()->getId()])],
            ['label' => $product->getLabel()]
        ];

        return $this->render('product/show.html.twig', compact('product', 'productFields', 'breadcrumb'));
    }
}
