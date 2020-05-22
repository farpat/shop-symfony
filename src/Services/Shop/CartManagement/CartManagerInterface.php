<?php

namespace App\Services\Shop\CartManagement;

interface CartManagerInterface
{

    /**
     * @return ['quantity' => $quantity, 'productReferenceId' => $productReferenceId]
     */
    public function deleteItem(int $productReferenceId): array;

    /**
     * @return ['quantity' => $quantity, 'productReferenceId' => $productReferenceId]
     */
    public function patchItem(int $quantity, int $productReferenceId): array;

    /**
     * @return ['quantity' => $quantity, 'productReferenceId' => $productReferenceId]
     */
    public function addItem(int $quantity, int $productReferenceId): array;

    public function getItems(): array;

    /**
     * Get items without fetching reference via database
     * @return array
     */
    public function getPureItems(): array;
}