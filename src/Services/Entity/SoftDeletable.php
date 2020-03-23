<?php

namespace App\Services\Entity;

trait SoftDeletable
{
    /**
     * @ORM\Column(type="datetime")
     */
    protected $deleted_at = null;

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

    public function getDeletedAt (): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }
}