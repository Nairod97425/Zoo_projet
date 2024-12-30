<?php

namespace App\Entity;

use App\Repository\ConsultationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
#[ORM\Table(name: 'Consultation')]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $idConsultation = null;

    #[ORM\ManyToOne(targetEntity: Animal::class, inversedBy: 'consultations')]
    #[ORM\JoinColumn(name: 'id_animal', referencedColumnName: 'id')]
    private ?Animal $animal = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'consultations')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $nombreDeConsultation = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $healthStatus;

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->idConsultation;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(Animal $animal): self
    {
        $this->animal = $animal;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getNombreDeConsultation(): ?int
    {
        return $this->nombreDeConsultation;
    }

    public function setNombreDeConsultation(?int $nombreDeConsultation): self
    {
        $this->nombreDeConsultation = $nombreDeConsultation;
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
}