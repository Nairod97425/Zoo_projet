<?php
namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'User')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', unique: true)]
    private string $Email;

    #[ORM\Column(type: 'string')]
    private string $motDePasse;

    #[ORM\Column(type: 'json')]
    private array $Roles = [];

    // Getters and Setters
    public function getId_admin(): ?int
    {
        return $this->id;
    }

    public function setId_admin(?int $id_admin): self
    {
        $this->id = $id_admin;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->Email;
    }

    public function setEmail(string $email): self
    {
        $this->Email = $email;
        return $this;
    }

    public function getMot_de_passe(): string
    {
        return $this->motDePasse;
    }

    public function setMot_de_passe(string $mot_de_passe): self
    {
        $this->motDePasse = $mot_de_passe;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->motDePasse;
    }

    public function getRoles(): array
    {
        // Ensure roles are always returned as an array
        $roles = $this->Roles;
        // Guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->Roles = $roles;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->Email;
    }

    public function eraseCredentials(): void
    {
        // Clear any sensitive data from the user object
        // In this example, there is nothing to clear
    }
}
