<?php

namespace App\Services\Entity;

trait Creatable
{
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @param \DateTimeInterface $created_at
     *
     * @return self
     */
    public function setCreatedAt (\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt (): ?\DateTimeInterface
    {
        return $this->created_at;
    }
}