<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderItemRepository")
 */
class OrderItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $amountExcludingTaxes;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $amountIncludingTaxes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductReference")
     * @ORM\JoinColumn(nullable=false)
     */
    private $productReference;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Orderable", inversedBy="items")
     * @ORM\JoinTable(name="orderable")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orderable;

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getQuantity (): ?int
    {
        return $this->quantity;
    }

    public function setQuantity (int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAmountExcludingTaxes (): ?string
    {
        return $this->amountExcludingTaxes;
    }

    public function setAmountExcludingTaxes (string $amountExcludingTaxes): self
    {
        $this->amountExcludingTaxes = $amountExcludingTaxes;

        return $this;
    }

    public function getAmountIncludingTaxes (): ?string
    {
        return $this->amountIncludingTaxes;
    }

    public function setAmountIncludingTaxes (string $amountIncludingTaxes): self
    {
        $this->amountIncludingTaxes = $amountIncludingTaxes;

        return $this;
    }

    public function getProductReference (): ?ProductReference
    {
        return $this->productReference;
    }

    public function setProductReference (?ProductReference $productReference): self
    {
        $this->productReference = $productReference;

        return $this;
    }

    public function getOrderable(): ?Orderable
    {
        return $this->orderable;
    }

    public function setOrderable(?Orderable $orderable): self
    {
        $this->orderable = $orderable;

        return $this;
    }
}
