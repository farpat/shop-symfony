<?php

namespace App\Controller\Front;

use App\Services\Shop\CartManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route(name="app_cart_")
 */
class CartController extends AbstractController
{

    /**
     * @var CartManager
     */
    private $cartManager;

    public function __construct (CartManager $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * @Route("/cart-items", name="store_item", methods={"POST"})
     */
    public function storeItem (Request $request, EntityManagerInterface $entityManager)
    {
        $orderItem = $this->cartManager->storeItem(
            $request->request->getInt('quantity'),
            $request->request->getInt('productReferenceId')
        );

        $entityManager->flush();

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncode]);

        return new Response($serializer->serialize($orderItem, 'json'));
    }

    /**
     * @Route("/cart-items/{productReferenceId}", name="patch_item", methods={"PATCH"})
     */
    public function patchItem (Request $request, int $productReferenceId, EntityManagerInterface $entityManager)
    {
        $orderItem = $this->cartManager->patchItem(
            $request->request->getInt('quantity'),
            $productReferenceId
        );

        $entityManager->flush();

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncode]);

        return new Response($serializer->serialize($orderItem, 'json'));
    }

    /**
     * @Route("/cart-items/{productReferenceId}", name="delete_item", methods={"DELETE"})
     */
    public function deleteItem (Request $request, int $productReferenceId, EntityManagerInterface $entityManager)
    {
        $orderItem = $this->cartManager->deleteItem(
            $productReferenceId
        );

        $entityManager->flush();

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncode]);

        return new Response($serializer->serialize($orderItem, 'json'));
    }

    /**
     * @Route("/purchase-cart", name="purchase", methods={"POST", "GET"})
     */
    public function purchase (Request $request)
    {

    }
}
