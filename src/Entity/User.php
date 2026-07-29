<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $resetPasswordToken = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $resetPasswordTokenExpiresAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): self
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    public function getResetPasswordTokenExpiresAt(): ?\DateTimeImmutable
    {
        return $this->resetPasswordTokenExpiresAt;
    }

    public function setResetPasswordTokenExpiresAt(?\DateTimeImmutable $resetPasswordTokenExpiresAt): self
    {
        $this->resetPasswordTokenExpiresAt = $resetPasswordTokenExpiresAt;

        return $this;
    }

    public function clearResetPasswordToken(): self
    {
        $this->resetPasswordToken = null;
        $this->resetPasswordTokenExpiresAt = null;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store temporary sensitive data, clear it here
    }
}
