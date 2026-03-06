<?php

namespace Pulecal\Service\Command;

use Pulecal\Service\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-root-user',
    description: 'Creates the root user based on environment variables',
)]
class CreateRootUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private string $rootUsername,
        private string $rootEmail,
        private string $rootPassword
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (empty($this->rootUsername) || empty($this->rootPassword)) {
            $io->warning('ROOT_USERNAME or ROOT_PASSWORD not set. Skipping root user creation.');
            return Command::SUCCESS;
        }

        $userRepository = $this->entityManager->getRepository(User::class);
        $existingUser = $userRepository->findOneBy(['username' => $this->rootUsername]);

        if ($existingUser) {
            $io->info(sprintf('User "%s" already exists.', $this->rootUsername));
            return Command::SUCCESS;
        }

        $user = new User();
        $user->setUsername($this->rootUsername);
        $user->setEmail($this->rootEmail ?: 'root@localhost');
        $user->setRoles(['ROLE_ROOT']);
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, $this->rootPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Root user "%s" created successfully with ROLE_ROOT.', $this->rootUsername));

        return Command::SUCCESS;
    }
}
