<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;

class CreateAdminUserCommand extends Command
{
    protected static $defaultName = 'app:create-admin';
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;
    private UserRepository $userRepo;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher, UserRepository $userRepo)
    {
        parent::__construct();
        $this->em = $em;
        $this->hasher = $hasher;
        $this->userRepo = $userRepo;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create an admin user')
            ->addArgument('email', InputArgument::OPTIONAL, 'Admin email', 'admin@example.com')
            ->addArgument('password', InputArgument::OPTIONAL, 'Admin password', 'ChangeMe123!');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = (string) $input->getArgument('email');
        $password = (string) $input->getArgument('password');

        if ($this->userRepo->findOneBy(['email' => $email])) {
            $io->error(sprintf('User with email %s already exists.', $email));
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $hashed = $this->hasher->hashPassword($user, $password);
        $user->setPassword($hashed);

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('Admin user %s created.', $email));
        $io->text('Please change the password after first login.');

        return Command::SUCCESS;
    }
}
