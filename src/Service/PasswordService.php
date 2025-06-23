<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordService
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function hashPassword(string $plainPassword): string
    {
        return $this->passwordHasher->hashPassword(
            new Utilisateur(), // On utilise une instance temporaire
            $plainPassword
        );
    }
}
