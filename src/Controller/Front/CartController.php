<?php

namespace App\Controller\Front;

use App\Services\Shop\CartManagement\{CartManagerInCookie, CartManagerInDatabase, CartManagerInterface};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Cookie, JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(name="app_front_cart_")
 */
class CartController extends AbstractController
{
    private CartManagerInterface   $cartManager;
    private SerializerInterface    $serializer;
    private EntityManagerInterface $entityManager;
    private TranslatorInterface    $translator;

    public function __construct(
        CartManagerInterface $cartManager,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ) {
        $this->cartManager = $cartManager;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
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
        $response = new JsonResponse($orderItem);

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
}
