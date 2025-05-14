<?php

namespace App\Enum;

enum EtatRendezVous: string
{
    case NORMAL = 'normal';
    case URGENCE = 'urgence';
    case SOUS_SURVEILLANCE = 'sous_surveillance';
}
