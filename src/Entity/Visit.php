<?php

namespace App\Entity;

use App\Services\Entity\Creatable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VisitRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "product" = "App\Entity\ProductVisit",
 *     "category" = "App\Entity\CategoryVisit"
 * })
 */
abstract class Visit
{
    use Creatable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $ip_address;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    protected $user;

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getIpAddress (): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress (string $ip_address): self
    {
        $this->ip_address = $ip_address;

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

    public function getVisitable ()
    {
        if ($this->product_id !== null) {
            return $this->getProduct();
        }
        if ($this->category_id !== null) {
            return $this->getCategory();
        }
        return null;
    }
}
