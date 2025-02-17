<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    // Add the ManyToMany relationship to the Role entity
    #[ORM\ManyToMany(targetEntity: Roles::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'users_roles')] // The join table
    private Collection $rolesList;

    public function __construct()
    {
        // Initialize the rolesList as a collection to manage the ManyToMany relationship
        $this->rolesList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    // Getter and setter for the rolesList ManyToMany relationship
    public function getRolesList(): Collection
    {
        return $this->rolesList;
    }

    public function addRole(Roles $role): static
    {
        if (!$this->rolesList->contains($role)) {
            $this->rolesList[] = $role;
        }

        return $this;
    }

    public function removeRole(Roles $role): static
    {
        $this->rolesList->removeElement($role);

        return $this;
    }
}
