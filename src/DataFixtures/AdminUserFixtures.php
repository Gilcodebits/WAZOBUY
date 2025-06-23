<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserFixtures
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function createAdmin(EntityManagerInterface $entityManager): void
    {
        $admin = new Utilisateur();
        $admin->setEmail('admin@test.com');
        $admin->setPassword(
            $this->passwordHasher->hashPassword(
                $admin,
                'password'
            )
        );
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setNom('Admin');
        $admin->setPrenom('Admin');

        $entityManager->persist($admin);
        $entityManager->flush();
    }
}
