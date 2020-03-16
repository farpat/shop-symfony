<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PasswordResetRepository")
 */
class PasswordReset
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getEmail (): ?string
    {
        return $this->email;
    }

    public function setEmail (string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getToken (): ?string
    {
        return $this->token;
    }

    public function setToken (string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getCreatedAt (): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt (\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
