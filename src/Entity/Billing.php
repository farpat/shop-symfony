<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BillingRepository")
 * @ORM\Table(indexes={@Index(name="number_index", columns={"number"})})
 */
class Billing extends Orderable
{
    public const ORDERED_STATUS = 'ORDERED';
    public const DELIVRED_STATUS = 'DELIVRED';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="billings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getNumber (): ?string
    {
        return $this->number;
    }

    public function setNumber (string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getStatus (): ?string
    {
        return $this->status;
    }

    public function setStatus (string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getBillingPath (): string
    {
        return "{$this->getUser()->getId()}/{$this->getNumber()}.pdf";
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
