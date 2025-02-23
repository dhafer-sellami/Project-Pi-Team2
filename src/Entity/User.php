<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, Ordonance>
     */
    #[ORM\OneToMany(targetEntity: Ordonance::class, mappedBy: 'patientId')]
    private Collection $ordonances;

    public function __construct()
    {
        $this->ordonances = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Ordonance>
     */
    public function getOrdonances(): Collection
    {
        return $this->ordonances;
    }

    public function addOrdonance(Ordonance $ordonance): static
    {
        if (!$this->ordonances->contains($ordonance)) {
            $this->ordonances->add($ordonance);
            $ordonance->setPatientId($this);
        }

        return $this;
    }

    public function removeOrdonance(Ordonance $ordonance): static
    {
        if ($this->ordonances->removeElement($ordonance)) {
            // set the owning side to null (unless already changed)
            if ($ordonance->getPatientId() === $this) {
                $ordonance->setPatientId(null);
            }
        }

        return $this;
    }
}
