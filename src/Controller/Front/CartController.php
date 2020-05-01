<?php

namespace App\Controller\Front;

use App\Services\Shop\CartManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $orderItem = $this->cartManager->addItem(
            $request->request->getInt('quantity'),
            $request->request->getInt('productReferenceId')
        );

        if ($this->getUser()) {
            $this->entityManager->flush();
        }

        return new JsonResponse($orderItem);
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

        if ($this->getUser()) {
            $this->entityManager->flush();
        }

        return new JsonResponse($orderItem);
    }

    /**
     * @Route("/cart-items/{productReferenceId}", name="delete_item", methods={"DELETE"})
     */
    public function deleteItem (Request $request, int $productReferenceId)
    {
        $orderItem = $this->cartManager->deleteItem(
            $productReferenceId
        );

        if ($this->getUser()) {
            $this->entityManager->flush();
        }

        return new JsonResponse($orderItem);
    }

    /**
     * @Route("/purchase-cart", name="purchase", methods={"POST", "GET"})
     */
    public function purchase (Request $request)
    {

    }
}
