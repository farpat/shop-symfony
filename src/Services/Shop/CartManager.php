<?php

namespace App\Services\Shop;


use App\Entity\Cart;
use App\Entity\OrderItem;
use App\Entity\ProductReference;
use App\Entity\User;
use App\Repository\ProductReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CartManager
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var ProductReferenceRepository
     */
    private $productReferenceRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct (Security $security, ProductReferenceRepository $productReferenceRepository, EntityManagerInterface $entityManager)
    {
        $this->user = $security->getUser();
        $this->productReferenceRepository = $productReferenceRepository;
        $this->entityManager = $entityManager;
    }

    private function checkQuantity (int $quantity)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException("The quantity must be greather than 1");
        }
    }

    private function getProductReference (int $productReferenceId): ProductReference
    {
        $productReference = $this->productReferenceRepository->find($productReferenceId);
        if ($productReference === null) {
            throw new \InvalidArgumentException("The product reference doesn't exist!");
        }

        return $productReference;
    }

    public function getCart (): Cart
    {
        $cart = $this->user->getCart();

        if ($cart !== null) {
            return $cart;
        }

        $cart = (new Cart)->setUser($this->user);

        $this->entityManager->persist($cart);

        return $cart;
    }

    public function deleteItem (int $productReferenceId): OrderItem
    {
        $productReference = $this->getProductReference($productReferenceId);

        $cart = $this->getCart();
        if (($orderItem = $cart->getOrderItem($productReference)) === null) {
            throw new \InvalidArgumentException("The product reference is not in cart");
        }

        $cart->removeItem($orderItem);

        if ($cart->getItemsCount() === 0) {
            $this->entityManager->remove($cart);
        }
    }

    public function patchItem (int $quantity, int $productReferenceId): OrderItem
    {
        $this->checkQuantity($quantity);
        $productReference = $this->getProductReference($productReferenceId);

        $cart = $this->getCart();
        if (($orderItem = $cart->getOrderItem($productReference)) === null) {
            throw new \InvalidArgumentException("The product reference is not in cart");
        }

        $oldAmountExcludingTaxes = $orderItem->getAmountExcludingTaxes();
        $oldAmountIncludingTaxes = $orderItem->getAmountIncludingTaxes();

        return $orderItem
            ->setQuantity($quantity)
            ->setAmountExcludingTaxes(($quantity * $productReference->getUnitPriceExcludingTaxes()) - $oldAmountExcludingTaxes)
            ->setAmountIncludingTaxes(($quantity * $productReference->getUnitPriceIncludingTaxes()) - $oldAmountIncludingTaxes);
    }

    public function storeItem (int $quantity, int $productReferenceId): OrderItem
    {
        $this->checkQuantity($quantity);
        $productReference = $this->getProductReference($productReferenceId);

        $cart = $this->getCart();
        if ($cart->getOrderItem($productReference)) {
            throw new \InvalidArgumentException("The product reference is already in cart");
        }

        $orderItem = (new OrderItem)
            ->setQuantity($quantity)
            ->setAmountExcludingTaxes($quantity * $productReference->getUnitPriceExcludingTaxes())
            ->setAmountIncludingTaxes($quantity * $productReference->getUnitPriceIncludingTaxes())
            ->setProductReference($productReference);

        $this->entityManager->persist($orderItem);

        $cart->addItem($orderItem);

        return $orderItem;
    }
}