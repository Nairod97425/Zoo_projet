<?php
// src/Entity/CompteRendu.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompteRenduRepository")
 */
#[ORM\Entity(repositoryClass: 'App\Repository\CompteRenduRepository')]
class CompteRendu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $description;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Animal')]
    #[ORM\JoinColumn(name: 'animal_id', referencedColumnName: 'id_Animal')]
    private ?Animal $animal;


    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): self
    {
        $this->animal = $animal;
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
}
