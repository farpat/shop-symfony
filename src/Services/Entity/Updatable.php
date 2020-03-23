<?php

namespace App\Services\Entity;

trait Updatable
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @param DateTimeInterface|null $updated_at
     *
     * @return self
     */
    public function setUpdatedAt (?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt (): ?\DateTimeInterface
    {
        return $this->updated_at;
    }
}