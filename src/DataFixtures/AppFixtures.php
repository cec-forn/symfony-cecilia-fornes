<?php

namespace App\DataFixtures;

use App\Entity\ContractType;
use App\Entity\Sector;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    const USER_NBR = 10;
    const SECTORS = ['RH', 'Informatique', 'Comptabilité', 'Direction'];
    const CONTRACTS_TYPES = ['CDI', 'CDD', 'Intérim'];
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        $userAdmin = new User();
        $userAdmin
            ->setEmail('rh@hb.com')
            ->setPassword('azerty123')
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setPicture("https://thispersondoesnotexist.com/")
            ->setRoles(["ROLE_ADMIN"]);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $userAdmin,
            $userAdmin->getPassword()
        );
        $userAdmin->setPassword($hashedPassword);

        $manager->persist($userAdmin);

        $sectors = [];
        for ($i = 0; $i < count(self::SECTORS); $i++) {
            $sector = new Sector();

            $sector
                ->setName(self::SECTORS[$i]);

            $sectors[] = $sector;
            $manager->persist($sector);
        }

        $contracts = [];
        for ($i = 0; $i < count(self::CONTRACTS_TYPES); $i++) {
            $contractType = new ContractType();

            $contractType
                ->setName(self::CONTRACTS_TYPES[$i]);

            $contracts[] = $contractType;
            $manager->persist($contractType);
        }

        for ($i = 0; $i < self::USER_NBR; $i++) {
            $user = new User();

            $user
                ->setEmail($faker->email())
                ->setPassword($faker->password())
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setPicture("https://thispersondoesnotexist.com/")
                ->setRoles(["ROLE_USER"])
                ->setContract($faker->randomElement($contracts))
                ->setSector($faker->randomElement($sectors));

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
