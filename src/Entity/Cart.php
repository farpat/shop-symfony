<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CartRepository")
 */
class Cart extends Orderable
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="cart", cascade={"persist", "remove"})
     */
    private $user;

    public function getUser (): ?User
    {
        return $this->user;
    }

    public function setUser (?User $user): self
    {
        $this->user = $user;

        // set (or unset) the owning side of the relation if necessary
        $newCart = null === $user ? null : $this;
        if ($user->getCart() !== $newCart) {
            $user->setCart($newCart);
        }

        return $this;
    }

    public function getOrderItem (ProductReference $productReference): ?OrderItem
    {
        foreach ($this->items as $item) {
            if ($item->getProductReference()->getId() === $productReference->getId()) {
                return $item;
            }
        }

        return null;
    }
}
