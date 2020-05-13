<?php

namespace App\Controller\Front;

use App\Services\Shop\CartManagement\CartManagerInCookie;
use App\Services\Shop\CartManagement\CartManagerInDatabase;
use App\Services\Shop\CartManagement\CartManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route(name="app_cart_")
 */
class CartController extends AbstractController
{
    private CartManagerInterface $cartManager;
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;
    /**
     * @var Request
     */
    private Request $request;

    public function __construct(
        CartManagerInterface $cartManager,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $this->cartManager = $cartManager;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/cart-items", name="store_item", methods={"POST"})
     */
    public function storeItem(Request $request)
    {
        $orderItem = $this->cartManager->addItem(
            $request->request->getInt('quantity'),
            $productReferenceId = $request->request->getInt('productReferenceId')
        );

        if ($this->cartManager instanceof CartManagerInDatabase) {
            $this->entityManager->flush();
        }

        return $this->returnJsonResponseFromCartManager($orderItem);
    }

    private function returnJsonResponseFromCartManager(array $orderItem): JsonResponse
    {
        $response = new JsonResponse($orderItem); //TODO renvoyer ['quantity' => (int)$quantity, 'reference' => (object)$reference]

        if ($this->cartManager instanceof CartManagerInCookie) {
            $response->headers->setCookie(
                new Cookie($this->cartManager::COOKIE_KEY, serialize($this->cartManager->getPureItems()))
            );
        }

        return $response;
    }

    /**
     * @Route("/cart-items/{productReferenceId}", name="patch_item", methods={"PATCH"})
     */
    public function patchItem(int $productReferenceId, Request $request)
    {
        $orderItem = $this->cartManager->patchItem(
            $request->request->getInt('quantity'),
            $productReferenceId
        );

        if ($this->cartManager instanceof CartManagerInDatabase) {
            $this->entityManager->flush();
        }

        return $this->returnJsonResponseFromCartManager($orderItem);
    }

    /**
     * @Route("/cart-items/{productReferenceId}", name="delete_item", methods={"DELETE"})
     */
    public function deleteItem(int $productReferenceId)
    {
        $orderItem = $this->cartManager->deleteItem($productReferenceId);

        if ($this->cartManager instanceof CartManagerInDatabase) {
            $this->entityManager->flush();
        }

        return $this->returnJsonResponseFromCartManager($orderItem);
    }

    /**
     * @Route("/purchase-cart", name="purchase", methods={"POST", "GET"})
     */
    public function purchase()
    {

    }
}
