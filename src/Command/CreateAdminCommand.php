<?php

namespace App\Command;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UtilisateurRepository $utilisateurRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $adminEmail = 'admin@test.com';
        $adminPassword = 'password';

        $admin = $this->utilisateurRepository->findOneBy(['email' => $adminEmail]);

        if (!$admin) {
            $admin = new Utilisateur();
            $admin->setEmail($adminEmail);
            $admin->setNom('Admin');
            $admin->setPrenom('Admin');
            $output->writeln('Utilisateur admin non trouvé, création en cours...');
        } else {
            $output->writeln('Utilisateur admin trouvé, mise à jour du mot de passe...');
        }

        $admin->setPassword(
            $this->passwordHasher->hashPassword(
                $admin,
                $adminPassword
            )
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $output->writeln('Utilisateur admin créé/mis à jour avec succès !');
        $output->writeln('Email: ' . $adminEmail);
        $output->writeln('Mot de passe: ' . $adminPassword);
        
        return Command::SUCCESS;
    }
}
