<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CategoryVisit extends Visit
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="visits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    public function getCategory(): ?Product
    {
        return $this->category;
    }

    public function setCategory(?Product $category): self
    {
        $this->category = $category;

        return $this;
    }
}
