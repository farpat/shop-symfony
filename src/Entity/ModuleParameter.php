<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ModuleParameter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="array")
     */
    private $value = [];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Module", inversedBy="parameters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $module;

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

    public function getDescription (): ?string
    {
        return $this->description;
    }

    public function setDescription (?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getValue (): array
    {
        return $this->value;
    }

    public function setValue (array $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getModule (): ?Module
    {
        return $this->module;
    }

    public function setModule (?Module $module): self
    {
        $this->module = $module;

        return $this;
    }
}
