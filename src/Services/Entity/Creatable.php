<?php

namespace App\Services\Entity;

trait Creatable
{
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @param DateTimeInterface $created_at
     *
     * @return Creatable
     */
    public function setCreatedAt (\DateTimeInterface $created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt (): ?\DateTimeInterface
    {
        return $this->created_at;
    }
}