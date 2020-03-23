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
     * @return Updatable
     */
    public function setUpdatedAt (?\DateTimeInterface $updated_at)
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt (): ?\DateTimeInterface
    {
        return $this->updated_at;
    }
}