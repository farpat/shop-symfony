<?php

namespace App\Services\Shop\CartManagement;

use App\Entity\OrderItem;

interface CartManagerInterface
{

    /**
     * @return array{quantity: int, reference: array}
     */
    public function deleteItem(int $productReferenceId): array;

    /**
     * @return array{quantity: int, reference: array}
     */
    public function patchItem(int $quantity, int $productReferenceId): array;

    /**
     * @return array{quantity: int, reference: array}
     */
    public function addItem(int $quantity, int $productReferenceId): array;

    public function getItems(): array;

    /**
     * Get items without fetching reference via database
     * @return OrderItem[]
     */
    public function getPureItems(): array;
}