<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ProductVisit extends Visit
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="visits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    public function getProduct (): ?Product
    {
        return $this->product;
    }

    public function setProduct (?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
