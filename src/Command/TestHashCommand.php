<?php

namespace App\Command;

use App\Entity\Utilisateur;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestHashCommand extends Command
{
    protected static $defaultName = 'app:test-hash';

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $password = 'password';
        $user = new Utilisateur();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );
        
        $output->writeln('Mot de passe: ' . $password);
        $output->writeln('Hash: ' . $hashedPassword);
        
        return Command::SUCCESS;
    }
}
