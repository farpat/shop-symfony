<?php

namespace App\Entity;

use App\Services\Support\Str;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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
    private $unitPriceExcludingTaxes = 0;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $unitPriceIncludingTaxes = 0;

    /**
     * @ORM\Column(type="json", nullable=false)
     */
    private $filledProductFields = [];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productReferences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Image")
     */
    private $mainImage;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image")
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getUnitPriceExcludingTaxes(): float
    {
        return $this->unitPriceExcludingTaxes;
    }

    public function setUnitPriceExcludingTaxes(float $unitPriceExcludingTaxes): self
    {
        $this->unitPriceExcludingTaxes = $unitPriceExcludingTaxes;

        return $this;
    }

    public function getPriceOfTaxes(): float
    {
        return $this->unitPriceIncludingTaxes - $this->unitPriceExcludingTaxes;
    }

    public function getUnitPriceIncludingTaxes(): float
    {
        return $this->unitPriceIncludingTaxes;
    }

    public function setUnitPriceIncludingTaxes(float $unitPriceIncludingTaxes): self
    {
        $this->unitPriceIncludingTaxes = $unitPriceIncludingTaxes;

        return $this;
    }

    public function getFilledProductFields(): ?array
    {
        return $this->filledProductFields;
    }

    public function setFilledProductFields(array $filledProductFields): self
    {
        $this->filledProductFields = $filledProductFields;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getMainImage(): ?Image
    {
        return $this->mainImage;
    }

    public function setMainImage(?Image $mainImage): self
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
        }

        return $this;
    }
}
