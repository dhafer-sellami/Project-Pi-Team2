<?php

namespace App\DataFixtures;

use App\Entity\PriseMedicament;
use App\Entity\Medicament;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        echo "Loading fixtures...\n";
        // Création d'un utilisateur patient
        $user = new User();
        $user->setEmail('patient@meditrack.fr');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password')
        );
        $manager->persist($user);

        // Création d'un médicament
        $medicament = new Medicament();
        $medicament->setNom('Paracétamol')
            ->setDosage('500mg')
            ->setFrequence('Toutes les 6 heures');
        $manager->persist($medicament);

        // Création d'une prise de médicament programmée
        $prise = new PriseMedicament();
        $prise->setDateHeurePrise(new \DateTimeImmutable('+25 minutes'))
            ->setPris(false)
            ->setPatient($user)
            ->setMedicament($medicament);

        $manager->persist($prise);

        // Création d'une prise passée pour tester les rapports
        $prisePassee = new PriseMedicament();
        $prisePassee->setDateHeurePrise(new \DateTimeImmutable('-2 hours'))
            ->setPris(true)
            ->setPatient($user)
            ->setMedicament($medicament);

        $manager->persist($prisePassee);

        $manager->flush();
        echo "Fixtures loaded successfully.\n"; 
    }
}