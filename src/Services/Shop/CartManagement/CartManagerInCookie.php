<?php

namespace App\Services\Shop\CartManagement;


use App\Entity\{ProductReference};
use App\Repository\ProductReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CartManagerInCookie implements CartManagerInterface
{
    public const COOKIE_KEY = 'cart-items';

    private ProductReferenceRepository $productReferenceRepository;
    private EntityManagerInterface     $entityManager;
    private array                      $items;
    private ?Request                   $request;
    private NormalizerInterface        $normalizer;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductReferenceRepository $productReferenceRepository,
        ?Request $request,
        NormalizerInterface $normalizer
    ) {
        $this->productReferenceRepository = $productReferenceRepository;
        $this->entityManager = $entityManager;
        $this->request = $request;
        $this->items = ($request && $request->cookies->has(self::COOKIE_KEY)) ? unserialize((string)$request->cookies->get(self::COOKIE_KEY)) : [];
        $this->normalizer = $normalizer;
    }

    /**
     * @return array{quantity: int, reference: array}
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function deleteItem(int $productReferenceId): array
    {
        if (!array_key_exists($productReferenceId, $this->items)) {
            throw new InvalidArgumentException("The product reference is not in cart");
        }
        $productReference = $this->getProductReference($productReferenceId);

        unset($this->items[$productReferenceId]);

        return [
            'quantity'  => 0,
            'reference' => (array)$this->normalizer->normalize($productReference, 'json')
        ];
    }

    private function getProductReference(int $productReferenceId): ProductReference
    {
        if (($productReference = $this->productReferenceRepository->find($productReferenceId)) === null) {
            throw new InvalidArgumentException("The product reference doesn't exist!");
        }

        return $productReference;
    }

    public function getPureItems(): array
    {
        return $this->items;
    }

    /**
     * @return array<int, array{quantity: int, reference: array}>
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getItems(): array
    {
        $items = [];

        if (empty($this->items)) {
            return $items;
        }

        $references = [];

        foreach ($this->productReferenceRepository->getWithAllRelations(array_keys($this->items)) as $reference) {
            $references[(int)$reference->getId()] = $reference;
        }

        return (array)$this->normalizer->normalize(array_map(fn($item) => [
            'quantity'  => $item['quantity'],
            'reference' => $references[$item['referenceId']]
        ], $this->items), 'json');
    }

    /**
     * @return array{quantity: int, reference: array}
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function patchItem(int $quantity, int $productReferenceId): array
    {
        $this->checkQuantity($quantity);
        if (!array_key_exists($productReferenceId, $this->items)) {
            throw new InvalidArgumentException("The product reference is not in cart");
        }
        $productReference = $this->getProductReference($productReferenceId);

        $this->items[$productReferenceId]['quantity'] = $quantity;

        return [
            'quantity'  => $quantity,
            'reference' => (array)$this->normalizer->normalize($productReference, 'json')
        ];
    }

    private function checkQuantity(int $quantity): void
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException("The quantity ($quantity) must be greather than 1");
        }
    }

    /**
     * @return array{quantity: int, reference: array}
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function addItem(int $quantity, int $productReferenceId): array
    {
        $this->checkQuantity($quantity);
        if (array_key_exists($productReferenceId, $this->items)) {
            throw new InvalidArgumentException("The product reference is already in cart");
        }
        $productReference = $this->getProductReference($productReferenceId);

        $this->items[$productReferenceId] = [
            'quantity'    => $quantity,
            'referenceId' => $productReferenceId
        ];

        return [
            'quantity'  => $quantity,
            'reference' => (array)$this->normalizer->normalize($productReference, 'json')
        ];
    }
}