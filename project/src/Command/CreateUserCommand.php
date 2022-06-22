<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;


#[AsCommand(
    name: 'app:create-user',
    description: 'Add a short description for your command',
)]

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:user';
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    // getting the interfaces inside the class, autowiring!
    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasherInterface){
        $this->entityManager = $em;
        $this->passwordHasher = $passwordHasherInterface;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'email used for the new account!')
            ->addArgument('password', InputArgument::REQUIRED, 'password used for the new account!')
        ;
    }

    // custom command function for creating an user on the website, with arg. for email and password
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg_email = $input->getArgument('email');
        $arg_password = $input->getArgument('password');

        // check if arg. are filled
        if ($arg_email || $arg_password) {
            $io->note(sprintf('You used the email adress: %s', $arg_email));
            $io->note(sprintf('You used the password: %s', $arg_password));
            
            // TODO: check if email is already used
            $user = New User();
            
            $user->setEmail($arg_email);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'test'));
            $user->setRoles('ROLE_ADMIN');
        
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $io->success('User has been created!');
        }

        return Command::SUCCESS;
    }
}
