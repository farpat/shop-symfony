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

    public function __construct (CartManagerInterface $cartManager, SerializerInterface $serializer, EntityManagerInterface $entityManager)
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
        $this->cartManager->addItem(
            $request->request->getInt('quantity'),
            $productReferenceId = $request->request->getInt('productReferenceId')
        );

        if ($this->cartManager instanceof CartManagerInDatabase) {
            $this->entityManager->flush();
        }

        return $this->returnJsonResponseFromCartManager($productReferenceId);
    }

    private function returnJsonResponseFromCartManager (int $productReferenceId): JsonResponse
    {
        $pureItems = $this->cartManager->getPureItems();

        $response = new JsonResponse($pureItems[$productReferenceId] ?? 'OK');

        if ($this->cartManager instanceof CartManagerInCookie) {
            $response->headers->setCookie(
                new Cookie($this->cartManager::COOKIE_KEY, serialize($pureItems))
            );
        }

        return $response;
    }

    /**
     * @Route("/cart-items/{productReferenceId}", name="patch_item", methods={"PATCH"})
     */
    public function patchItem (int $productReferenceId, Request $request)
    {
        $this->cartManager->patchItem(
            $request->request->getInt('quantity'),
            $productReferenceId
        );

        if ($this->cartManager instanceof CartManagerInDatabase) {
            $this->entityManager->flush();
        }

        return $this->returnJsonResponseFromCartManager($productReferenceId);
    }

    /**
     * @Route("/cart-items/{productReferenceId}", name="delete_item", methods={"DELETE"})
     */
    public function deleteItem (int $productReferenceId)
    {
        $this->cartManager->deleteItem($productReferenceId);

        if ($this->cartManager instanceof CartManagerInDatabase) {
            $this->entityManager->flush();
        }

        return $this->returnJsonResponseFromCartManager($productReferenceId);
    }

    /**
     * @Route("/purchase-cart", name="purchase", methods={"POST", "GET"})
     */
    public function purchase ()
    {

    }
}
