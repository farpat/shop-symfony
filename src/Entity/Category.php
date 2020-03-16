<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\Column(type="text")
     */
    private $nomenclature;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_last;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Image")
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="category")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductVisit", mappedBy="product", orphanRemoval=true)
     */
    private $visits;

    public function __construct ()
    {
        $this->products = new ArrayCollection();
        $this->visits = new ArrayCollection();
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

    public function getNomenclature (): ?string
    {
        return $this->nomenclature;
    }

    public function setNomenclature (string $nomenclature): self
    {
        $this->nomenclature = $nomenclature;

        return $this;
    }

    public function getSlug (): ?string
    {
        return $this->slug;
    }

    public function setSlug (string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription (): ?string
    {
        return $this->description;
    }

    public function setDescription (string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsLast (): ?bool
    {
        return $this->is_last;
    }

    public function setIsLast (bool $is_last): self
    {
        $this->is_last = $is_last;

        return $this;
    }

    public function getImage (): ?Image
    {
        return $this->image;
    }

    public function setImage (?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts (): Collection
    {
        return $this->products;
    }

    public function addProduct (Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct (Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductVisit[]
     */
    public function getVisits (): Collection
    {
        return $this->visits;
    }

    public function addVisit (ProductVisit $visit): self
    {
        if (!$this->visits->contains($visit)) {
            $this->visits[] = $visit;
            $visit->setProduct($this);
        }

        return $this;
    }

    public function removeVisit (ProductVisit $visit): self
    {
        if ($this->visits->contains($visit)) {
            $this->visits->removeElement($visit);
            // set the owning side to null (unless already changed)
            if ($visit->getProduct() === $this) {
                $visit->setProduct(null);
            }
        }

        return $this;
    }
}
