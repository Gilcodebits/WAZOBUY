<?php

namespace App\Service;

use App\DataFixtures\AdminUserFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminInitializer
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager
    ) {}

    public function initializeAdmin(): void
    {
        $adminFixtures = new AdminUserFixtures($this->passwordHasher);
        $adminFixtures->createAdmin($this->entityManager);
    }
}
