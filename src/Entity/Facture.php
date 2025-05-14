<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?RendezVous $idrdv = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "Le numéro est obligatoire.")]
    #[Assert\Type(type:"integer", message: "Le numéro doit être un entier.")]
    private ?int $prix = null;



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

    

   
}
