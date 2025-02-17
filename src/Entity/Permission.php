<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    // ManyToMany relationship with Role entity
    #[ORM\ManyToMany(targetEntity: Roles::class, inversedBy: 'permissions')]
    #[ORM\JoinTable(name: 'roles_permissions')] // The join table
    private Collection $roles;

    public function __construct()
    {
        // Initialize the roles collection for the ManyToMany relationship
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    // Getter and setter for the roles ManyToMany relationship
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Roles $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Roles $role): static
    {
        $this->roles->removeElement($role);

        return $this;
    }
}
