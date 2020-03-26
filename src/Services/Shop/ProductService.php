<?php

namespace App\Services\Shop;


use App\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductService
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct (UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getShowUrl (Product $product)
    {
        return $this->urlGenerator->generate('product.show', [
            'categorySlug' => $product->getCategory()->getSlug(),
            'categoryId'   => $product->getCategory()->getId(),
            'productSlug'  => $product->getSlug(),
            'productId'    => $product->getId()
        ]);
    }
}