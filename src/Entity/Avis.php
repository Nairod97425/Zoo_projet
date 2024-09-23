<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AvisRepository;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_Avis = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $Pseudo;

    #[ORM\Column(type: 'text')]
    private string $Avis;

    #[ORM\Column(type: 'boolean')]
    private bool $Statut;

    #[ORM\ManyToOne(targetEntity: Habitat::class)]
    #[ORM\JoinColumn(name: 'id_habitat', referencedColumnName: 'id')]
    private ?Habitat $habitat = null;


    public function getId(): ?int
    {
        return $this->id_Avis;
    }

    public function getPseudo(): string
    {
        return $this->Pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->Pseudo = $pseudo;
        return $this;
    }

    public function getAvis(): string
    {
        return $this->Avis;
    }

    public function setAvis(string $avis): self
    {
        $this->Avis = $avis;
        return $this;
    }

    public function isApproved(): bool
    {
        return $this->Statut;
    }

    public function setIsApproved(bool $isApproved): self
    {
        $this->Statut = $isApproved;
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
}
