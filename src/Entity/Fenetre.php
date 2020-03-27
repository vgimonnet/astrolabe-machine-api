<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FenetreRepository")
 */
class Fenetre
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
     * @ORM\Column(type="smallint")
     */
    private $width;

    /**
     * @ORM\Column(type="smallint")
     */
    private $height;

    /**
     * @ORM\Column(type="smallint")
     */
    private $posx;

    /**
     * @ORM\Column(type="smallint")
     */
    private $posy;

    /**
     * @ORM\Column(type="boolean")
     */
    private $veille;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_youtube;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_playlist;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getPosx(): ?int
    {
        return $this->posx;
    }

    public function setPosx(int $posx): self
    {
        $this->posx = $posx;

        return $this;
    }

    public function getPosy(): ?int
    {
        return $this->posy;
    }

    public function setPosy(int $posy): self
    {
        $this->posy = $posy;

        return $this;
    }

    public function getVeille(): ?bool
    {
        return $this->veille;
    }

    public function setVeille(bool $veille): self
    {
        $this->veille = $veille;

        return $this;
    }

    public function getIsYoutube(): ?bool
    {
        return $this->is_youtube;
    }

    public function setIsYoutube(bool $is_youtube): self
    {
        $this->is_youtube = $is_youtube;

        return $this;
    }

    public function getIsPlaylist(): ?bool
    {
        return $this->is_playlist;
    }

    public function setIsPlaylist(bool $is_playlist): self
    {
        $this->is_playlist = $is_playlist;

        return $this;
    }
}
