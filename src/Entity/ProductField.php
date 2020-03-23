<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductFieldRepository")
 */
class ProductField
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_required;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="product_fields")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getType (): ?string
    {
        return $this->type;
    }

    public function setType (string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLabel (): ?string
    {
        return $this->label;
    }

    public function setLabel (string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getIsRequired (): ?bool
    {
        return $this->is_required;
    }

    public function setIsRequired (bool $is_required): self
    {
        $this->is_required = $is_required;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
