<?php

namespace App\Command;

use App\Service\AdminInitializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitializeAdminCommand extends Command
{
    protected static $defaultName = 'app:init-admin';

    public function __construct(private AdminInitializer $adminInitializer)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->adminInitializer->initializeAdmin();
        $output->writeln('Utilisateur admin créé avec succès !');
        $output->writeln('Email: admin@test.com');
        $output->writeln('Mot de passe: password');
        
        return Command::SUCCESS;
    }
}
