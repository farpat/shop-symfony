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
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $alt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url_thumbnail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alt_thumbnail;

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getUrl (): ?string
    {
        return $this->url;
    }

    public function setUrl (string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAlt (): ?string
    {
        return $this->alt;
    }

    public function setAlt (string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getUrlThumbnail (): ?string
    {
        return $this->url_thumbnail;
    }

    public function setUrlThumbnail (?string $url_thumbnail): self
    {
        $this->url_thumbnail = $url_thumbnail;

        return $this;
    }

    public function getAltThumbnail (): ?string
    {
        return $this->alt_thumbnail;
    }

    public function setAltThumbnail (?string $alt_thumbnail): self
    {
        $this->alt_thumbnail = $alt_thumbnail;

        return $this;
    }
}
