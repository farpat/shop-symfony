<?php

namespace App\Services\Shop;


use App\Entity\{Cart, OrderItem, ProductReference, User};
use App\Repository\ProductReferenceRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class CartManager
{
    private const COOKIE_KEY = 'cart-items';
    private ProductReferenceRepository $productReferenceRepository;
    private EntityManagerInterface $entityManager;
    private ?Request $request;
    private ?User $user;

    public function __construct (Security $security, EntityManagerInterface $entityManager, RequestStack $requestStack, ProductReferenceRepository $productReferenceRepository)
    {
        $this->user = $security->getUser();
        $this->request = $requestStack->getCurrentRequest();
        $this->productReferenceRepository = $productReferenceRepository;
        $this->entityManager = $entityManager;
    }

    public function deleteItem (int $productReferenceId): OrderItem
    {
        $productReference = $this->getProductReference($productReferenceId);

        if ($this->user) { //database
            $cart = $this->getCart($this->user);
            if (($orderItem = $cart->getOrderItem($productReference)) === null) {
                throw new \InvalidArgumentException("The product reference is not in cart");
            }

            $cart->removeItem($orderItem);

            if ($cart->getItemsCount() === 0) {
                $this->entityManager->remove($cart);
            }
        } else { //cookie

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

    public function getCart (User $user): Cart
    {
        $cart = $user->getCart();

        if ($cart !== null) {
            return $cart;
        }

        $cart = (new Cart)->setUser($this->user);

        $this->entityManager->persist($cart);

        return $cart;
    }

    public function patchItem (int $quantity, int $productReferenceId): OrderItem
    {
        $this->checkQuantity($quantity);
        $productReference = $this->getProductReference($productReferenceId);

        if ($this->user) { //database
            $cart = $this->getCart($this->user);
            if (($orderItem = $cart->getOrderItem($productReference)) === null) {
                throw new \InvalidArgumentException("The product reference is not in cart");
            }

            $oldAmountExcludingTaxes = $orderItem->getAmountExcludingTaxes();
            $oldAmountIncludingTaxes = $orderItem->getAmountIncludingTaxes();

            return $orderItem
                ->setQuantity($quantity)
                ->setAmountExcludingTaxes(($quantity * $productReference->getUnitPriceExcludingTaxes()) - $oldAmountExcludingTaxes)
                ->setAmountIncludingTaxes(($quantity * $productReference->getUnitPriceIncludingTaxes()) - $oldAmountIncludingTaxes);
        } else { //cookie

        }
    }

    private function checkQuantity (int $quantity)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException("The quantity must be greather than 1");
        }
    }

    public function addItem (int $quantity, int $productReferenceId): OrderItem
    {
        $this->checkQuantity($quantity);
        $productReference = $this->getProductReference($productReferenceId);

        if ($this->user) { //database
            $cart = $this->getCart($this->user);
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
        } else { //cookie
            $items = $this->getItems();

            $orderItem = (new OrderItem)
                ->setQuantity($quantity)
                ->setAmountExcludingTaxes($quantity * $productReference->getUnitPriceExcludingTaxes())
                ->setAmountIncludingTaxes($quantity * $productReference->getUnitPriceIncludingTaxes())
                ->setProductReference($productReference);

            $items->add($orderItem);

            $this->setCookieItems();

            return $orderItem;
        }
    }

    /**
     * @return OrderItem[]|Collection
     */
    public function getItems (): array
    {
        if ($this->request->getUser()) {
            return $this->getCart()->getItems();
        }

        return $this->request->cookies->get(self::COOKIE_KEY, []);
    }

    /**
     * @param OrderItem[] $items
     */
    private function setCookieItems (array $items)
    {
        $this->request->cookies->set(self::COOKIE_KEY, $items);
    }
}