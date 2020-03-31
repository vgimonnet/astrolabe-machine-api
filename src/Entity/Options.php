<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OptionsRepository")
 */
class Options
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="boolean")
     */
    private $veille;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $temps_veille_1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $temps_veille_2;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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

    public function getTempsVeille1(): ?int
    {
        return $this->temps_veille_1;
    }

    public function setTempsVeille1(?int $temps_veille_1): self
    {
        $this->temps_veille_1 = $temps_veille_1;

        return $this;
    }

    public function getTempsVeille2(): ?int
    {
        return $this->temps_veille_2;
    }

    public function setTempsVeille2(?int $temps_veille_2): self
    {
        $this->temps_veille_2 = $temps_veille_2;

        return $this;
    }
}
