<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: 'App\Repository\HabitatRepository')]
#[ORM\Table(name: 'habitat')]
class Habitat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $Name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $Description = null;

    #[ORM\OneToMany(targetEntity: 'App\Entity\Avis', mappedBy: 'habitat', cascade: ['persist', 'remove'])]
    private Collection $Avis;

    #[ORM\OneToMany(targetEntity: 'App\Entity\Animal', mappedBy: 'habitat', cascade: ['persist', 'remove'])]
    private Collection $Animals;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $images = [];

    public function __construct()
    {
        $this->Avis = new ArrayCollection();
        $this->Animals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->Name;
    }

    public function setName(string $name): self
    {
        $this->Name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $description): self
    {
        $this->Description = $description;
        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->Avis;
    }

    public function addAvis(Avis $avis): self
    {
        if (!$this->Avis->contains($avis)) {
            $this->Avis->add($avis);
            $avis->setHabitat($this);
        }

        return $this;
    }

    public function removeAvis(Avis $avis): self
    {
        if ($this->Avis->removeElement($avis)) {
            if ($avis->getHabitat() === $this) {
                $avis->setHabitat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Animal>
     */
    public function getAnimals(): Collection
    {
        return $this->Animals;
    }

    public function addAnimal(Animal $animal): self
    {
        if (!$this->Animals->contains($animal)) {
            $this->Animals->add($animal);
            $animal->setHabitat($this);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): self
    {
        if ($this->Animals->removeElement($animal)) {
            if ($animal->getHabitat() === $this) {
                $animal->setHabitat(null);
            }
        }

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images = $images ?? [];
        return $this;
    }

    public function addImage(string $image): self
    {
        if (!in_array($image, $this->images, true)) {
            $this->images[] = $image;
        }

        return $this;
    }

    public function removeImage(string $image): self
    {
        $this->images = array_values(array_filter($this->images, fn($img) => $img !== $image));

        return $this;
    }
}
