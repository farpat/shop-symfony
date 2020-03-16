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
    private $amount_excluding_taxes;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $amount_including_taxes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductReference")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product_reference;

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
        return $this->amount_excluding_taxes;
    }

    public function setAmountExcludingTaxes (string $amount_excluding_taxes): self
    {
        $this->amount_excluding_taxes = $amount_excluding_taxes;

        return $this;
    }

    public function getAmountIncludingTaxes (): ?string
    {
        return $this->amount_including_taxes;
    }

    public function setAmountIncludingTaxes (string $amount_including_taxes): self
    {
        $this->amount_including_taxes = $amount_including_taxes;

        return $this;
    }

    public function getProductReference (): ?ProductReference
    {
        return $this->product_reference;
    }

    public function setProductReference (?ProductReference $product_reference): self
    {
        $this->product_reference = $product_reference;

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
