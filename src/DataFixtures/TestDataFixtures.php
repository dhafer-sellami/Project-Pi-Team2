<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Medicament;
use App\Entity\PriseMedicament;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestDataFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un patient
        $patient = new User();
        $patient->setEmail('patient@example.com');
        $patient->setRoles(['ROLE_PATIENT']);
        $hashedPassword = $this->passwordHasher->hashPassword($patient, 'password123');
        $patient->setPassword($hashedPassword);
        $manager->persist($patient);

        // Utiliser le médicament existant (Paracétamol)
        $medicament = $manager->getRepository(Medicament::class)->findOneBy(['nom' => 'Paracétamol']);

        if ($medicament) {
            // Créer des prises de médicament pour aujourd'hui
            $now = new \DateTimeImmutable();
            
            // Prise dans 15 minutes
            $priseMedicament1 = new PriseMedicament();
            $priseMedicament1->setPatient($patient);
            $priseMedicament1->setMedicament($medicament);
            $priseMedicament1->setDateHeurePrise($now->modify('+15 minutes'));
            $priseMedicament1->setPris(false);
            $manager->persist($priseMedicament1);

            // Prise dans 2 heures
            $priseMedicament2 = new PriseMedicament();
            $priseMedicament2->setPatient($patient);
            $priseMedicament2->setMedicament($medicament);
            $priseMedicament2->setDateHeurePrise($now->modify('+2 hours'));
            $priseMedicament2->setPris(false);
            $manager->persist($priseMedicament2);
        }

        $manager->flush();
    }
}
