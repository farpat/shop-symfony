<?php

namespace App\FormData;


use App\Entity\User;
use App\Validator\Unique;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateMyAddressesFormData
{

    private ?int $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    public function __construct(array $data)
    {
        $this
            ->setEmail($data['email'])
            ->setName($data['name'])
            ->setId(data['id']);
    }

    public function updateUser(User $user): User
    {
        return $user
            ->setName($this->getName())
            ->setEmail($this->getEmail());
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return RegisterFormData
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }
}