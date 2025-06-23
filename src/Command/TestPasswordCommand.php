<?php

namespace App\Command;

use App\Service\PasswordService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestPasswordCommand extends Command
{
    protected static $defaultName = 'app:test-password';

    public function __construct(private PasswordService $passwordService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $password = 'password';
        $hashedPassword = $this->passwordService->hashPassword($password);
        
        $output->writeln('Mot de passe: ' . $password);
        $output->writeln('Hash: ' . $hashedPassword);
        
        return Command::SUCCESS;
    }
}
