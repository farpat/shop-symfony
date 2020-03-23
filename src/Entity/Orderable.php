<?php

namespace App\Entity;

use App\Services\Entity\Creatable;
use App\Services\Entity\Updatable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *  "cart" = "App\Entity\Cart",
 *  "billing" = "App\Entity\Billing"
 * })
 */
abstract class Orderable
{
    use Updatable, Creatable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Address")
     */
    protected $delivered_address;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    protected $items_count;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $total_amount_excluding_taxes;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $total_amount_including_taxes;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="orderable", orphanRemoval=true)
     */
    protected $items;

    public function __construct ()
    {
        $this->created_at = new \DateTime;
        $this->items = new ArrayCollection();
    }

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getComment (): ?string
    {
        return $this->comment;
    }

    public function setComment (?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDeliveredAddress (): ?Address
    {
        return $this->delivered_address;
    }

    public function setDeliveredAddress (?Address $delivered_address): self
    {
        $this->delivered_address = $delivered_address;

        return $this;
    }

    public function getItemsCount (): ?int
    {
        return $this->items_count;
    }

    public function setItemsCount (int $items_count): self
    {
        $this->items_count = $items_count;

        return $this;
    }

    public function getTotalAmountExcludingTaxes (): ?string
    {
        return $this->total_amount_excluding_taxes;
    }

    public function setTotalAmountExcludingTaxes (string $total_amount_excluding_taxes): self
    {
        $this->total_amount_excluding_taxes = $total_amount_excluding_taxes;

        return $this;
    }

    public function getTotalAmountIncludingTaxes (): ?string
    {
        return $this->total_amount_including_taxes;
    }

    public function setTotalAmountIncludingTaxes (string $total_amount_including_taxes): self
    {
        $this->total_amount_including_taxes = $total_amount_including_taxes;

        return $this;
    }

    public function getUser (): ?User
    {
        return $this->user;
    }

    public function setUser (?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getItems (): Collection
    {
        return $this->items;
    }

    public function addItem (OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setOrderable($this);
        }

        return $this;
    }

    public function removeItem (OrderItem $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getOrderable() === $this) {
                $item->setOrderable(null);
            }
        }

        return $this;
    }
}
