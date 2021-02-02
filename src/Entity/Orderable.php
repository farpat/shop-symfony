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
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $deliveryAddress;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    protected $itemsCount = 0;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $totalAmountExcludingTaxes = 0;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $totalAmountIncludingTaxes = 0;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="orderable", orphanRemoval=true)
     * @var OrderItem[]|ArrayCollection
     */
    protected $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDeliveryAddress(): ?Address
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?Address $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getIncludingTaxes()
    {
        return $this->totalAmountIncludingTaxes - $this->totalAmountExcludingTaxes;
    }

    public function getPriceOfTaxes()
    {
        return $this->totalAmountIncludingTaxes - $this->totalAmountExcludingTaxes;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $this->setItemsCount($this->getItemsCount() + 1);
            $this->setTotalAmountExcludingTaxes($this->getTotalAmountExcludingTaxes() + $item->getAmountExcludingTaxes());
            $this->setTotalAmountIncludingTaxes($this->getTotalAmountIncludingTaxes() + $item->getAmountIncludingTaxes());

            $item->setOrderable($this);
        }

        return $this;
    }

    public function getItemsCount(): ?int
    {
        return $this->itemsCount;
    }

    public function setItemsCount(int $itemsCount): self
    {
        $this->itemsCount = $itemsCount;

        return $this;
    }

    public function getTotalAmountExcludingTaxes(): ?string
    {
        return $this->totalAmountExcludingTaxes;
    }

    public function setTotalAmountExcludingTaxes(string $totalAmountExcludingTaxes): self
    {
        $this->totalAmountExcludingTaxes = $totalAmountExcludingTaxes;

        return $this;
    }

    public function getTotalAmountIncludingTaxes(): ?float
    {
        return $this->totalAmountIncludingTaxes;
    }

    public function setTotalAmountIncludingTaxes(string $totalAmountIncludingTaxes): self
    {
        $this->totalAmountIncludingTaxes = $totalAmountIncludingTaxes;

        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);

            $this->setItemsCount($this->getItemsCount() - 1);
            $this->setTotalAmountExcludingTaxes($this->getTotalAmountExcludingTaxes() - $item->getAmountExcludingTaxes());
            $this->setTotalAmountIncludingTaxes($this->getTotalAmountIncludingTaxes() - $item->getAmountIncludingTaxes());


            // set the owning side to null (unless already changed)
            if ($item->getOrderable() === $this) {
                $item->setOrderable(null);
            }
        }

        return $this;
    }
}
