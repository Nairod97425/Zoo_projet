<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180 , unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    /**
     * Non-persisted plain password.
     *
     * @Assert\NotBlank(groups={"registration"}, message="Please enter a password.")
     * @Assert\Length(
     *     min=10,
     *     max=4096,
     *     minMessage="Your password should be at least {{ limit }} characters.",
     *     groups={"registration"}
     * )
     * @Assert\Regex(
     *     pattern="/[A-Z]/",
     *     message="Your password must contain at least one uppercase letter.",
     *     groups={"registration"}
     * )
     * @Assert\Regex(
     *     pattern="/\d/",
     *     message="Your password must contain at least one number.",
     *     groups={"registration"}
     * )
     * @Assert\Regex(
     *     pattern="/[^\w]/",
     *     message="Your password must contain at least one special character (e.g., !@#$%^&*).",
     *     groups={"registration"}
     * )
     */
    private ?string $plainPassword = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Effacer toutes les données sensibles (par exemple, le mot de passe en clair)
        $this->plainPassword = null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    // Getter et setter pour le mot de passe plain
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }
}
