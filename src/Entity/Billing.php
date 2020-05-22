<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BillingRepository")
 * @ORM\Table(indexes={@Index(name="number_index", columns={"number"})})
 */
class Billing extends Orderable
{
    public const ORDERED_STATUS = 'ORDERED';
    public const DELIVRED_STATUS = 'DELIVRED';

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="billings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     */
    private $addressText;

    /**
     * @ORM\Column(type="text")
     */
    private $addressLine1;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $addressLine2;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $addressPostalCode;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $addressCity;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $addressCountry;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $addressCountryCode;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=6)
     */
    private $addressLatitude;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=6)
     */
    private $addressLongitude;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $userEmail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userName;

    public static function createFromCart(Cart $cart, int $currentNumber): self
    {
        return (new static)
            ->setDeliveredAddress($cart->getDeliveredAddress())
            ->setUser($cart->getUser())
            ->setNumber($cart->getCreatedAt()->format('Y-m') . '-' . $currentNumber)
            ->setStatus(self::ORDERED_STATUS)
            ->setComment($cart->getComment())
            ->setItemsCount($cart->getItemsCount())
            ->setTotalAmountExcludingTaxes($cart->getTotalAmountExcludingTaxes())
            ->setTotalAmountIncludingTaxes($cart->getTotalAmountIncludingTaxes());
    }

    public function setDeliveredAddress(?Address $deliveredAddress): Orderable
    {
        $this
            ->setAddressText($deliveredAddress->getText())
            ->setAddressLine1($deliveredAddress->getLine1())
            ->setAddressLine2($deliveredAddress->getLine2())
            ->setAddressPostalCode($deliveredAddress->getPostalCode())
            ->setAddressCity($deliveredAddress->getCity())
            ->setAddressCountry($deliveredAddress->getCountry())
            ->setAddressCountryCode($deliveredAddress->getCountryCode())
            ->setAddressLatitude($deliveredAddress->getLatitude())
            ->setAddressLongitude($deliveredAddress->getLongitude());

        return parent::setDeliveredAddress($deliveredAddress);
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getBillingPath(): string
    {
        return "{$this->getUser()->getId()}/{$this->getNumber()}.pdf";
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this
            ->setUserEmail($user->getEmail())
            ->setUserName($user->getName())
            ->user = $user;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getAddressText(): ?string
    {
        return $this->addressText;
    }

    public function setAddressText(string $addressText): self
    {
        $this->addressText = $addressText;

        return $this;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getAddressPostalCode(): ?string
    {
        return $this->addressPostalCode;
    }

    public function setAddressPostalCode(?string $addressPostalCode): self
    {
        $this->addressPostalCode = $addressPostalCode;

        return $this;
    }

    public function getAddressCity(): ?string
    {
        return $this->addressCity;
    }

    public function setAddressCity(?string $addressCity): self
    {
        $this->addressCity = $addressCity;

        return $this;
    }

    public function getAddressCountryCode(): ?string
    {
        return $this->addressCountryCode;
    }

    public function setAddressCountryCode(?string $addressCountryCode): self
    {
        $this->addressCountryCode = $addressCountryCode;

        return $this;
    }

    public function getAddressCountry(): ?string
    {
        return $this->addressCountry;
    }

    public function setAddressCountry(?string $addressCountry): self
    {
        $this->addressCountry = $addressCountry;

        return $this;
    }

    public function getAddressLatitude(): ?string
    {
        return $this->addressLatitude;
    }

    public function setAddressLatitude(?string $addressLatitude): self
    {
        $this->addressLatitude = $addressLatitude;

        return $this;
    }

    public function getAddressLongitude(): ?string
    {
        return $this->addressLongitude;
    }

    public function setAddressLongitude(?string $addressLongitude): self
    {
        $this->addressLongitude = $addressLongitude;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): self
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }
}
