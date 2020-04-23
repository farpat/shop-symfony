<?php

namespace App\Controller\Front;

use App\Services\Shop\CartManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route(name="app_cart_")
 */
class CartController extends AbstractController
{
    private CartManager $cartManager;
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;

    public function __construct (CartManager $cartManager, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->cartManager = $cartManager;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/cart-items", name="store_item", methods={"POST"})
     */
    public function storeItem (Request $request)
    {
        $orderItem = $this->cartManager->storeItem(
            $request->request->getInt('quantity'),
            $request->request->getInt('productReferenceId')
        );

        $this->entityManager->flush();

        return new Response($this->serializer->serialize($orderItem, 'json'));
    }

    /**
     * @Route("/cart-items/{productReferenceId}", name="patch_item", methods={"PATCH"})
     */
    public function patchItem (Request $request, int $productReferenceId)
    {
        $orderItem = $this->cartManager->patchItem(
            $request->request->getInt('quantity'),
            $productReferenceId
        );

        $this->entityManager->flush();

        return new Response($this->serializer->serialize($orderItem, 'json'));
    }

    /**
     * @Route("/cart-items/{productReferenceId}", name="delete_item", methods={"DELETE"})
     */
    public function deleteItem (Request $request, int $productReferenceId)
    {
        $orderItem = $this->cartManager->deleteItem(
            $productReferenceId
        );

        $this->entityManager->flush();

        return new Response($this->serializer->serialize($orderItem, 'json'));
    }

    /**
     * @Route("/purchase-cart", name="purchase", methods={"POST", "GET"})
     */
    public function purchase (Request $request)
    {

    }
}
