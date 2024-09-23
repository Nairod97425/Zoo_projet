<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Service;

#[ORM\Entity]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private ?string $phone = null;

    #[ORM\ManyToOne(targetEntity: Service::class, inversedBy: 'employees')]
    #[ORM\JoinColumn(name: 'service_id', referencedColumnName: 'id', nullable: false)] // Assurez-vous que ça référence 'id'
    private ?Service $service = null;

    // Getters and Setters

    public function getIdEmployee(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;
        return $this;
    }
}
