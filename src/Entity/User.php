<?php

namespace App\Entity;

use App\Services\Entity\Creatable;
use App\Services\Entity\Updatable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{

    use Updatable, Creatable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $email_verified_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stripe_id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $remember_token;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Address", mappedBy="user", orphanRemoval=true)
     */
    private $addresses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Billing", mappedBy="user")
     */
    private $billings;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Cart", inversedBy="user", cascade={"persist", "remove"})
     */
    private $cart;

    public function __construct ()
    {
        $this->created_at = new \DateTime;
        $this->addresses = new ArrayCollection();
        $this->billings = new ArrayCollection();
    }

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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername (): string
    {
        return (string)$this->email;
    }

    public function isAdmin (): bool
    {
        return in_array('ROLE_ADMIN', $this->roles);
    }

    /**
     * @see UserInterface
     */
    public function getRoles (): array
    {
        return $this->roles;
    }

    public function setRoles (array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword (): string
    {
        return (string)$this->password;
    }

    public function setPassword (string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt ()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials ()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName (): ?string
    {
        return $this->name;
    }

    public function setName (string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmailVerifiedAt (): ?\DateTimeInterface
    {
        return $this->email_verified_at;
    }

    public function setEmailVerifiedAt (?\DateTimeInterface $email_verified_at): self
    {
        $this->email_verified_at = $email_verified_at;

        return $this;
    }

    public function getStripeId (): ?string
    {
        return $this->stripe_id;
    }

    public function setStripeId (?string $stripe_id): self
    {
        $this->stripe_id = $stripe_id;

        return $this;
    }

    public function getRememberToken (): ?string
    {
        return $this->remember_token;
    }

    public function setRememberToken (?string $remember_token): self
    {
        $this->remember_token = $remember_token;

        return $this;
    }

    /**
     * @return Collection|Address[]
     */
    public function getAddresses (): Collection
    {
        return $this->addresses;
    }

    public function addAddress (Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setUser($this);
        }

        return $this;
    }

    public function removeAddress (Address $address): self
    {
        if ($this->addresses->contains($address)) {
            $this->addresses->removeElement($address);
            // set the owning side to null (unless already changed)
            if ($address->getUser() === $this) {
                $address->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Billing[]
     */
    public function getBillings(): Collection
    {
        return $this->billings;
    }

    public function addBilling(Billing $billing): self
    {
        if (!$this->billings->contains($billing)) {
            $this->billings[] = $billing;
            $billing->setUser($this);
        }

        return $this;
    }

    public function removeBilling(Billing $billing): self
    {
        if ($this->billings->contains($billing)) {
            $this->billings->removeElement($billing);
            // set the owning side to null (unless already changed)
            if ($billing->getUser() === $this) {
                $billing->setUser(null);
            }
        }

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }
}
