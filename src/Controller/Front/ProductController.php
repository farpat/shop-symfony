<?php

namespace App\Controller\Front;

use App\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route(name="app_front_product_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/categories/{categorySlug}-{categoryId}/{productSlug}-{productId}", name="show", methods={"GET"},
     *     requirements={
     *     "categorySlug":"[a-z\d\-]+", "productSlug":"[a-z\d\-]+",
     *     "categoryId":"\d+", "productId":"\d+"
     * }
     *     )
     * @Entity("product", expr="repository.getWithAllRelations(productId)")
     */
    public function show(
        Product $product,
        string $categorySlug,
        int $categoryId,
        string $productSlug,
        NormalizerInterface $normalizer
    ) {
        if (
            $categorySlug !== $product->getCategory()->getSlug() ||
            $productSlug !== $product->getSlug() ||
            $categoryId !== $product->getCategory()->getId()
        ) {
            return $this->redirect($this->generateUrl('app_front_product_show', [
                'productId'    => $product->getId(),
                'productSlug'  => $product->getSlug(),
                'categoryId'   => $product->getCategory()->getId(),
                'categorySlug' => $product->getCategory()->getSlug(),
            ]));
        }

        $breadcrumb = [
            ['label' => 'category', 'url' => $this->generateUrl('app_front_category_index')],
            [
                'label' => $product->getCategory()->getLabel(),
                'url'   => $this->generateUrl('app_front_category_show', [
                    'categoryId'   => $categoryId,
                    'categorySlug' => $categorySlug
                ])
            ],
            ['label' => $product->getLabel()]
        ];

        return $this->render('product/show.html.twig', [
            'product'                     => $product,
            'normalizedProductReferences' => $normalizer->normalize($product->getProductReferences(), 'json'),
            'breadcrumb'                  => $breadcrumb,
        ]);
    }
}
