<?php
// src/Entity/Animal.php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id_Animal;

    #[ORM\Column(type: 'string', length: 50)]
    private string $Prenom;

    #[ORM\Column(type: 'string', length: 50)]
    private string $Race;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $Etat = null;

    #[ORM\ManyToOne(targetEntity: Habitat::class, inversedBy: 'Animals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Habitat $habitat = null;

    // Here's the change: A one-to-many relationship with the Image entity
    #[ORM\OneToMany(mappedBy: 'animal', targetEntity: Image::class, cascade: ['persist', 'remove'])]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id_Animal;
    }

    public function getPrenom(): string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->Prenom = $prenom;
        return $this;
    }

    public function getRace(): string
    {
        return $this->Race;
    }

    public function setRace(string $race): self
    {
        $this->Race = $race;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->Etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->Etat = $etat;
        return $this;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): self
    {
        $this->habitat = $habitat;
        return $this;
    }

    // Getter for images
    public function getImages(): Collection
    {
        return $this->images;
    }

    // Add an image to the animal
    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAnimal($this);
        }

        return $this;
    }

    // Remove an image from the animal
    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAnimal() === $this) {
                $image->setAnimal(null);
            }
        }

        return $this;
    }
}
