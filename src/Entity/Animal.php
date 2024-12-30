<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: 'App\Repository\AnimalRepository')]
#[ORM\Table(name: 'animal')]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $species;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $healthStatus;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $feedingSchedule;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $image = [];
    
    #[ORM\ManyToOne(targetEntity: Habitat::class, inversedBy: 'animals')]
    #[ORM\JoinColumn(nullable: false)]
    private $habitat;

    #[ORM\OneToMany(targetEntity: CompteRendu::class, mappedBy: 'animal')]
    private Collection $compteRendus;

    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'animal')]
    private Collection $consultations;

    public function __construct()
    {
        $this->compteRendus = new ArrayCollection();
        $this->consultations = new ArrayCollection();
    }

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSpecies(): string
    {
        return $this->species;
    }

    public function setSpecies(string $species): self
    {
        $this->species = $species;
        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getHealthStatus(): string
    {
        return $this->healthStatus;
    }

    public function setHealthStatus(string $healthStatus): self
    {
        $this->healthStatus = $healthStatus;
        return $this;
    }

    public function getFeedingSchedule(): string
    {
        return $this->feedingSchedule;
    }

    public function setFeedingSchedule(string $feedingSchedule): self
    {
        $this->feedingSchedule = $feedingSchedule;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): array
    {
        return $this->image;
    }

    public function setImage(array $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getCompteRendus(): Collection
    {
        return $this->compteRendus;
    }

    public function getConsultations(): Collection
    {
        return $this->consultations;
    }
}