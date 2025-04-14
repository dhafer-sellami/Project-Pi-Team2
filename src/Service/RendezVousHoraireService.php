<?php
namespace App\Service;  

class RendezVousHoraireService
{
    public function estHoraireValide(\DateTimeInterface $date): bool
    {
        $hr = (int) $date->format('H');
        $min = (int) $date->format('i');

        return !($hr < 8 || $hr >= 17 || ($min !== 0 && $min !== 30));
    }
}
