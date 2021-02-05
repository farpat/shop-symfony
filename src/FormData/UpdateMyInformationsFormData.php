<?php

namespace App\FormData;


use App\Entity\User;
use App\Validator\Unique;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Unique(field="email", entity="App\Entity\User")
 */
final class UpdateMyInformationsFormData
{
    private int $id;

    /**
     * @Assert\NotBlank()
     */
    private string $name;
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private string $email;

    public function __construct(array $data)
    {
        $this
            ->setEmail($data['email'])
            ->setName($data['name'])
            ->setId($data['id']);
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
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail(string $email): self
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

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
}