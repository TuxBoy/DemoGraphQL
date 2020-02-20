<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 */
class Movie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @Groups("group1")
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $allocine_id;

    /**
     * @Groups("group1")
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $title;

    /**
     * @Groups("group1")
     * @ORM\Column(type="text")
     */
    private string $synopsis;

    /**
     * @Groups("group1")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $poster;

    /**
     * @Groups("group1")
     * @ORM\ManyToOne(targetEntity="App\Entity\Director", inversedBy="movies")
     */
    private $director;

    /**
     * @Groups("group1")
     * @ORM\ManyToMany(targetEntity="App\Entity\Actor", inversedBy="movies")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $actors;

    public function __construct()
    {
        $this->actors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAllocineId(): ?int
    {
        return $this->allocine_id;
    }

    public function setAllocineId(?int $allocine_id): self
    {
        $this->allocine_id = $allocine_id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getDirector(): ?Director
    {
        return $this->director;
    }

    public function setDirector(?Director $director): self
    {
        $this->director = $director;

        return $this;
    }

    /**
     * @return Collection|Actor[]
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(Actor $actor): self
    {
        if (!$this->actors->contains($actor)) {
            $this->actors[] = $actor;
        }

        return $this;
    }

    public function removeActor(Actor $actor): self
    {
        if ($this->actors->contains($actor)) {
            $this->actors->removeElement($actor);
        }

        return $this;
    }
}
