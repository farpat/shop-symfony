<?php

namespace App\Controller\Front;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
    public function show (Product $product, string $categorySlug, int $categoryId, string $productSlug, ProductRepository $productRepository, SerializerInterface $serializer)
    {
        if ($categorySlug !== $product->getCategory()->getSlug() || $productSlug !== $product->getSlug() || $categoryId !== $product->getCategory()->getId()) {
            return $this->redirect($this->generateUrl('app_product_show', [
                'productId'    => $product->getId(),
                'productSlug'  => $product->getSlug(),
                'categoryId'   => $product->getCategory()->getId(),
                'categorySlug' => $product->getCategory()->getSlug(),
            ]));
        }

        $breadcrumb = [
            ['label' => 'category', 'url' => $this->generateUrl('app_category_index')],
            [
                'label' => $product->getCategory()->getLabel(),
                'url'   => $this->generateUrl('app_category_show', [
                    'categoryId'   => $categoryId,
                    'categorySlug' => $categorySlug
                ])
            ],
            ['label' => $product->getLabel()]
        ];

        return $this->render('product/show.html.twig', [
            'product'                 => $product,
            'productReferencesInJson' => $serializer->serialize($product->getProductReferences(), 'json'),
            'breadcrumb'              => $breadcrumb,
        ]);
    }
}
