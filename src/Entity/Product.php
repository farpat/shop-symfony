<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    const PER_PAGE = 9;
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
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $excerpt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Image")
     */
    private $mainImage;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="products")
     * @MaxDepth(1)
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="products")
     */
    private $tags;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tax")
     */
    private $taxes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductReference", mappedBy="product", orphanRemoval=true)
     */
    private $productReferences;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->taxes = new ArrayCollection();
        $this->productReferences = new ArrayCollection();
    }

    public function getMinUnitPriceIncludingTaxes(): float
    {
        $references = $this->getProductReferences();
        $min = $references[0]->getUnitPriceIncludingTaxes();
        foreach ($references as $reference) {
            if ($min > $reference->getUnitPriceIncludingTaxes()) {
                $min = $reference->getUnitPriceIncludingTaxes();
            }
        }

        return $min;
    }

    /**
     * @return Collection|ProductReference[]
     */
    public function getProductReferences(): Collection
    {
        return $this->productReferences;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): self
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * @return Collection|Tax[]
     */
    public function getTaxes(): Collection
    {
        return $this->taxes;
    }

    public function addTax(Tax $tax): self
    {
        if (!$this->taxes->contains($tax)) {
            $this->taxes[] = $tax;
        }

        return $this;
    }

    public function removeTax(Tax $tax): self
    {
        if ($this->taxes->contains($tax)) {
            $this->taxes->removeElement($tax);
        }

        return $this;
    }

    public function addProductReference(ProductReference $productReference): self
    {
        if (!$this->productReferences->contains($productReference)) {
            $this->productReferences[] = $productReference;
            $productReference->setProduct($this);
        }

        return $this;
    }

    public function removeProductReference(ProductReference $productReference): self
    {
        if ($this->productReferences->contains($productReference)) {
            $this->productReferences->removeElement($productReference);
            // set the owning side to null (unless already changed)
            if ($productReference->getProduct() === $this) {
                $productReference->setProduct(null);
            }
        }

        return $this;
    }
}
