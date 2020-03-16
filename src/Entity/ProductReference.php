<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductReferenceRepository")
 */
class ProductReference
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $unit_price_excluding_taxes;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $unit_price_including_taxes;

    /**
     * @ORM\Column(type="json")
     */
    private $filled_product_fields = [];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productReferences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Image")
     */
    private $main_image;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image")
     */
    private $images;

    public function __construct ()
    {
        $this->images = new ArrayCollection();
    }

    public function getId (): ?int
    {
        return $this->id;
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

    public function getUnitPriceExcludingTaxes (): ?string
    {
        return $this->unit_price_excluding_taxes;
    }

    public function setUnitPriceExcludingTaxes (string $unit_price_excluding_taxes): self
    {
        $this->unit_price_excluding_taxes = $unit_price_excluding_taxes;

        return $this;
    }

    public function getUnitPriceIncludingTaxes (): ?string
    {
        return $this->unit_price_including_taxes;
    }

    public function setUnitPriceIncludingTaxes (string $unit_price_including_taxes): self
    {
        $this->unit_price_including_taxes = $unit_price_including_taxes;

        return $this;
    }

    public function getFilledProductFields (): ?array
    {
        return $this->filled_product_fields;
    }

    public function setFilledProductFields (array $filled_product_fields): self
    {
        $this->filled_product_fields = $filled_product_fields;

        return $this;
    }

    public function getProduct (): ?Product
    {
        return $this->product;
    }

    public function setProduct (?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getMainImage (): ?Image
    {
        return $this->main_image;
    }

    public function setMainImage (?Image $main_image): self
    {
        $this->main_image = $main_image;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages (): Collection
    {
        return $this->images;
    }

    public function addImage (Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    public function removeImage (Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
        }

        return $this;
    }
}
