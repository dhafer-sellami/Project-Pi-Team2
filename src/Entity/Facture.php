<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?RendezVous $idrdv = null;

    #[ORM\Column(nullable: true)]
    private ?int $prix = null;

    #[ORM\ManyToOne]
    private ?user $idusr1 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdrdv(): ?RendezVous
    {
        return $this->idrdv;
    }

    public function setIdrdv(?RendezVous $idrdv): static
    {
        $this->idrdv = $idrdv;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getIdusr1(): ?user
    {
        return $this->idusr1;
    }

    public function setIdusr1(?user $idusr1): static
    {
        $this->idusr1 = $idusr1;

        return $this;
    }
}
