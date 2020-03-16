<?php

namespace App\Services\Entity;

trait Creatable
{
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    public function __construct ()
    {
        parent::__construct();
        $this->created_at = new \DateTime;
    }

    /**
     * @param mixed $created_at
     *
     * @return Creatable
     */
    public function setCreatedAt ($created_at)
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