<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



#[UniqueEntity(fields: ['date'], message: "Un rendez-vous existe déjà à cette date et heure. Veuillez choisir une autre heure.")]
#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type:"datetime", nullable:true)]
    #[Assert\NotBlank(message: "Veuillez sélectionner une date et une heure.")]
    #[Assert\GreaterThan("today", message: "La date doit être dans le futur.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne]
    private ?User $idusr = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(message: "L'adresse email  n'est pas valide.")]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    
    #[Assert\NotBlank(message: "Le numéro est obligatoire.")]
    #[Assert\Type(type:"integer", message: "Le numéro doit être un entier.")]
    #[Assert\Positive(message: "Le numéro doit être un entier positif.")]
    #[Assert\Length(
       min: 8,
       max: 8,
       exactMessage: "Le numéro doit contenir exactement 8 chiffres."
    )]
    private ?int $num = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "L'état est obligatoire.")]
    #[Assert\Type(type: "string", message: "L'état doit être une chaîne de caractères.")]
    private ?string $etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getIdusr(): ?user
    {
        return $this->idusr;
    }

    public function setIdusr(?user $idusr): static
    {
        $this->idusr = $idusr;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getNum(): ?int
    {
        return $this->num;
    }

    public function setNum(?int $num): static
    {
        $this->num = $num;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
    
}
