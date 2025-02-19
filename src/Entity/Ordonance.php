<?php

namespace App\Entity;

use App\Repository\OrdonanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdonanceRepository::class)]
class Ordonance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $notice = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'ordonances')]
    private ?User $doctorId = null;

    #[ORM\ManyToOne(inversedBy: 'ordonances')]
    private ?User $patientId = null;

    /**
     * @var Collection<int, Medicament>
     */
    #[ORM\ManyToMany(targetEntity: Medicament::class, inversedBy: 'ordonances')]
    private Collection $medicaments;

    public function __construct()
    {
        $this->medicaments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotice(): ?string
    {
        return $this->notice;
    }

    public function setNotice(string $notice): static
    {
        $this->notice = $notice;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDoctorId(): ?User
    {
        return $this->doctorId;
    }

    public function setDoctorId(?User $doctorId): static
    {
        $this->doctorId = $doctorId;

        return $this;
    }

    public function getPatientId(): ?User
    {
        return $this->patientId;
    }

    public function setPatientId(?User $patientId): static
    {
        $this->patientId = $patientId;

        return $this;
    }

    /**
     * @return Collection<int, Medicament>
     */
    public function getMedicaments(): Collection
    {
        return $this->medicaments;
    }

    public function addMedicament(Medicament $medicament): static
    {
        if (!$this->medicaments->contains($medicament)) {
            $this->medicaments->add($medicament);
        }

        return $this;
    }

    public function removeMedicament(Medicament $medicament): static
    {
        $this->medicaments->removeElement($medicament);

        return $this;
    }
}
