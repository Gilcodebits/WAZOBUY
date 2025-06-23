<?php

namespace App\Command;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-vendeur',
    description: 'Crée un compte vendeur avec le rôle ROLE_VENDEUR'
)]
class CreateVendeurCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Cette commande crée un compte vendeur avec le rôle ROLE_VENDEUR');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Création d\'un compte vendeur');

        // Créer un nouvel utilisateur vendeur
        $vendeur = new Utilisateur();
        $vendeur->setPrenom('Vendeur');
        $vendeur->setNom('Test');
        $vendeur->setEmail('vendeur@wazobuy.com');
        $vendeur->setTelephone('+22912345678');
        $vendeur->setRoles(['ROLE_VENDEUR']);
        $vendeur->setEstActif(true);
        $vendeur->setDateCreation(new \DateTimeImmutable());

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($vendeur, 'password123');
        $vendeur->setPassword($hashedPassword);

        // Sauvegarder en base de données
        $this->entityManager->persist($vendeur);
        $this->entityManager->flush();

        $io->success([
            'Compte vendeur créé avec succès !',
            '',
            'Email: vendeur@wazobuy.com',
            'Mot de passe: password123',
            'Rôle: ROLE_VENDEUR',
            '',
            'Vous pouvez maintenant vous connecter au dashboard vendeur.'
        ]);

        return Command::SUCCESS;
    }
} 