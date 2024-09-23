<?php

namespace App\Entity;

use App\Repository\ConsultationRenduRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConsultationRepository")
 */
#[ORM\Entity(repositoryClass: ConsultationRenduRepository::class)]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_Consultation = null;

    #[ORM\ManyToOne(targetEntity: Habitat::class)]
    #[ORM\JoinColumn(name: 'id_habitat', referencedColumnName: 'id')]
    private ?Habitat $habitat = null;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Animal')]
    #[ORM\JoinColumn(name: 'animal_id', referencedColumnName: 'id_Animal')]
    private ?Animal $animal;


    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $Date = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $Nombre_de_consultation = null;

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id_Consultation;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(Habitat $habitat): self
    {
        $this->habitat = $habitat;
        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal; // Utilisation correcte de $animal
    }

    public function setAnimal(Animal $animal): self
    {
        $this->animal = $animal; // Utilisation correcte de $animal
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->Date = $date;
        return $this;
    }

    public function getNombreDeConsultation(): ?int
    {
        return $this->Nombre_de_consultation;
    }

    public function setNombreDeConsultation(?int $nombreDeConsultation): self
    {
        $this->Nombre_de_consultation = $nombreDeConsultation;
        return $this;
    }
}
