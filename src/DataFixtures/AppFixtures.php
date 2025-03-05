<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker ;
        private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = Factory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@boss.de');
         $password = $this->hasher->hashPassword($user, 'pass_1234');
        $user->setPassword($password);
        $user->setPseudo('Admin');
        $user->setIsVerified(true);
        $user->setCin('12340321');
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        for ($i =0 ; $i < 10; $i++) {
        $user = new User();
        $user->setEmail($this->faker->email());
        $user->setPseudo(mt_rand(0,1)==1 ? $this->faker->firstName() :'');
        $password = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($password);
        $user->setRoles(['ROLE_USER']);

        $user->setCin($this->faker->unique()->randomNumber(8, true));
        $user->setIsVerified($this->faker->boolean());
        $manager->persist($user);
        
    }

    $manager->flush();
}


        




}
