<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CartRepository")
 */
class Cart extends Orderable
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $webhookPaymentId;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="carts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @return mixed
     */
    public function getWebhookPaymentId()
    {
        return $this->webhookPaymentId;
    }

    /**
     * @param mixed $webhookPaymentId
     * @return Cart
     */
    public function setWebhookPaymentId($webhookPaymentId)
    {
        $this->webhookPaymentId = $webhookPaymentId;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}