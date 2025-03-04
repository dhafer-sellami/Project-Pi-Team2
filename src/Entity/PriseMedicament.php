<?php

namespace App\Entity;

use App\Repository\PriseMedicamentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriseMedicamentRepository::class)]
class PriseMedicament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateHeurePrise = null;

    #[ORM\Column]
    private ?bool $pris = false;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'prisesMedicaments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $patient = null;

    #[ORM\ManyToOne(targetEntity: Medicament::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medicament $medicament = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateHeurePrise(): ?\DateTimeImmutable
    {
        return $this->dateHeurePrise;
    }

    public function setDateHeurePrise(\DateTimeImmutable $dateHeurePrise): static
    {
        $this->dateHeurePrise = $dateHeurePrise;
        return $this;
    }

    public function isPris(): ?bool
    {
        return $this->pris;
    }

    public function setPris(bool $pris): static
    {
        $this->pris = $pris;
        return $this;
    }

    public function getPatient(): ?User
    {
        return $this->patient;
    }

    public function setPatient(?User $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    public function getMedicament(): ?Medicament
    {
        return $this->medicament;
    }

    public function setMedicament(?Medicament $medicament): static
    {
        $this->medicament = $medicament;
        return $this;
    }
}