<?php

namespace App\Services\Shop\CartManagement;


use App\Entity\{Cart, OrderItem, ProductReference, User};
use App\Repository\CartRepository;
use App\Repository\ProductReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CartManagerInDatabase implements CartManagerInterface
{
    private ProductReferenceRepository $productReferenceRepository;
    private EntityManagerInterface $entityManager;
    private User $user;
    private Cart $cart;
    private NormalizerInterface $normalizer;
    /** @var OrderItem[] $items */
    private array $items;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductReferenceRepository $productReferenceRepository,
        CartRepository $cartRepository,
        User $user,
        NormalizerInterface $normalizer
    ) {
        $this->productReferenceRepository = $productReferenceRepository;
        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->cart = $cartRepository->getCart($user) ?: $this->createCart();
        $this->items = $this->cart->getItems()->toArray();
        $this->normalizer = $normalizer;
    }

    public function createCart(): Cart
    {
        $this->entityManager->persist($cart = (new Cart)->setUser($this->user));
        return $cart;
    }

    public function deleteItem(int $productReferenceId): array
    {
        $productReference = $this->getProductReference($productReferenceId);
        if (($orderItem = $this->getOrderItem($productReference)) === null) {
            throw new InvalidArgumentException("The product reference is not in cart");
        }

        $this->cart->removeItem($orderItem);

        if ($this->cart->getItemsCount() === 0) {
            $this->entityManager->remove($this->cart);
        }

        return [
            'quantity' => 0,
            'reference' => $this->normalizer->normalize($orderItem->getProductReference(), 'json')
        ];
    }

    private function getProductReference(int $productReferenceId): ProductReference
    {
        $productReference = $this->productReferenceRepository->find($productReferenceId);
        if ($productReference === null) {
            throw new InvalidArgumentException("The product reference doesn't exist!");
        }

        return $productReference;
    }

    private function getOrderItem(ProductReference $productReference): ?OrderItem
    {
        foreach ($this->items as $item) {
            if ($item->getProductReference()->getId() === $productReference->getId()) {
                return $item;
            }
        }

        return null;
    }

    /**
     *
     * @return bool true if items in database is updated, otherwise false
     * @throws Exception
     */
    public function merge(array $cookieItems): bool
    {
        if (empty($cookieItems)) {
            return false;
        }

        $databaseItems = [];
        foreach ($this->items as $productReferenceId => $item) {
            $databaseItems[$item->getProductReference()->getId()] = $item;
        }

        $updates = [];
        $additions = [];
        foreach ($cookieItems as $productReferenceId => $cookieItem) {
            $databaseItem = $databaseItems[$productReferenceId] ?? null;

            if ($databaseItem !== null && $cookieItem['quantity'] > $databaseItem->getQuantity()) {
                $updates[$productReferenceId] = $cookieItem['quantity'];
            }

            if ($databaseItem === null) {
                $additions[$productReferenceId] = $cookieItem['quantity'];
            }
        }

        if (empty($updates) && empty($additions)) {
            return false;
        }

        foreach ($updates as $productReferenceId => $quantity) {
            $this->patchItem($quantity, $productReferenceId);
        }

        foreach ($additions as $productReferenceId => $quantity) {
            $this->addItem($quantity, $productReferenceId);
        }

        return true;
    }

    public function patchItem(int $quantity, int $productReferenceId): array
    {
        $this->checkQuantity($quantity);
        $productReference = $this->getProductReference($productReferenceId);
        if (($orderItem = $this->getOrderItem($productReference)) === null) {
            throw new InvalidArgumentException("The product reference is not in cart");
        }

        $orderItem
            ->setQuantity($quantity)
            ->setAmountExcludingTaxes($quantity * $productReference->getUnitPriceExcludingTaxes())
            ->setAmountIncludingTaxes($quantity * $productReference->getUnitPriceIncludingTaxes());

        $this->entityManager->persist($orderItem);

        return [
            'quantity' => $orderItem->getQuantity(),
            'reference' => $this->normalizer->normalize($orderItem->getProductReference(), 'json')
        ];
    }

    private function checkQuantity(int $quantity)
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException("The quantity ($quantity) must be greather than 1");
        }
    }

    public function addItem(int $quantity, int $productReferenceId): array
    {
        $this->checkQuantity($quantity);
        $productReference = $this->getProductReference($productReferenceId);
        if ($this->getOrderItem($productReference) !== null) {
            throw new Exception("The product reference is already in cart");
        }

        $orderItem = (new OrderItem)
            ->setQuantity($quantity)
            ->setAmountExcludingTaxes($quantity * $productReference->getUnitPriceExcludingTaxes())
            ->setAmountIncludingTaxes($quantity * $productReference->getUnitPriceIncludingTaxes())
            ->setProductReference($productReference);

        $this->entityManager->persist($orderItem);
        $this->cart->addItem($orderItem);

        return [
            'quantity' => $orderItem->getQuantity(),
            'reference' => $this->normalizer->normalize($orderItem->getProductReference(), 'json')
        ];
    }

    public function getPureItems(): array
    {
        return $this->items;
    }

    public function getItems(): array
    {
        $items = [];

        if (empty($this->items)) {
            return $items;
        }

        foreach ($this->items as $item) {
            $items[$item->getProductReference()->getId()] = [
                'quantity' => $item->getQuantity(),
                'reference' => $this->normalizer->normalize($item->getProductReference(), 'json')
            ];
        }

        return $items;
    }
}