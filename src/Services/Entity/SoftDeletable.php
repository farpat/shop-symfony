<?php

namespace App\Services\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait SoftDeletable
{
    /**
     * @ORM\Column(type="datetime")
     */
    protected $deletedAt = null;

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deletedAt;
    }

    /**
     * @param DateTimeInterface|null $deletedAt
     *
     * @return self
     */
    public function setDeletedAt(?DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    /**
     * @ORM\PreRemove()
     */
    public function deleteEntity()
    {
        $this->setDeletedAt(new DateTime);
    }
}