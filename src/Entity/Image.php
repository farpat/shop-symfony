<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
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
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $alt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $urlThumbnail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $altThumbnail;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getUrlThumbnail(): ?string
    {
        return $this->urlThumbnail;
    }

    public function setUrlThumbnail(?string $urlThumbnail): self
    {
        $this->urlThumbnail = $urlThumbnail;

        return $this;
    }

    public function getAltThumbnail(): ?string
    {
        return $this->altThumbnail;
    }

    public function setAltThumbnail(?string $altThumbnail): self
    {
        $this->altThumbnail = $altThumbnail;

        return $this;
    }
}
