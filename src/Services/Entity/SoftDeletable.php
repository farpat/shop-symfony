<?php

namespace App\Services\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SoftDeletable
{
    /**
     * @ORM\Column(type="datetime")
     */
    protected $deleted_at = null;

    public function getDeletedAt (): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    /**
     * @param \DateTimeInterface|null $deleted_at
     *
     * @return self
     */
    public function setDeletedAt (?\DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;
        return $this;
    }

    /**
     * @ORM\PreRemove()
     */
    public function deleteEntity ()
    {
        $this->setDeletedAt(new \DateTime);
    }
}