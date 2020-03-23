<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BillingRepository")
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
}
