<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaxRepository")
 */
class Tax
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
    private $label;

    /**
     * @ORM\Column(type="string", length=50, columnDefinition="ENUM('PERCENTAGE', 'UNITY')")
     */
    private $type;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $value;

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getLabel (): ?string
    {
        return $this->label;
    }

    public function setLabel (string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getType (): ?string
    {
        return $this->type;
    }

    public function setType (string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue (): ?string
    {
        return $this->value;
    }

    public function setValue (string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
