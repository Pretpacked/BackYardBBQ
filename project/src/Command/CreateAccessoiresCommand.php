<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Accessoire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;


#[AsCommand(
    name: 'app:create:accessoires',
    description: 'Generate all the accessoires used on the webpage inside the database',
)]
class CreateAccessoiresCommand extends Command
{
    protected static $defaultName = 'app:accessoire';
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    // getting the interfaces inside the class, autowiring!
    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasherInterface){
        $this->entityManager = $em;
        $this->passwordHasher = $passwordHasherInterface;
        parent::__construct();
    }

    protected function configure(): void
    {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pre_set_accessoires = array(
            array('houtskool', 'Houtskool (Latijn: carbo), ofwel verkoling van hout, ontleent zijn betekenis aan de ontleding (pyrolyse) van hout. Het gaat om een bewerking waarbij hout wordt verhit op een zodanige wijze dat slechts een beperkte hoeveelheid zuurstof kan toetreden. Het grootste deel van het hout verbrandt dan niet en de vluchtige bestanddelen verdampen. In tegenstelling tot hout, dat uit complexe moleculen bestaat, is houtskool nagenoeg zuiver koolstof. Houtskool komt chemisch dicht bij steenkool of steenkoolcokes of turfcokes.'),
            array('briketten', 'De Cocones hexagonal kokos briketten zijn door diverse barbecue kenners en professionals geprezen om de lange en constante warmte die ze afgeven. Een unieke zeshoek vorm zorgt voor een snelle warmte overdracht bij deze cocos briketten. De grote van een enkele briket is 50x50x58mm.')
        );

        for ($i=0; $i < count($pre_set_accessoires); $i++) {
            $element = $pre_set_accessoires[$i];

            // dd($this->entityManager->getRepository(Accessoire::class)->findBy(
            //     array('name' => $element[0])));

            if(count($this->entityManager->getRepository(Accessoire::class)->findBy(
                array('name' => $element[0]))) !== 0){
                $io->getErrorStyle()->error('Accessoire already found!!');
                continue;
            }else{
                $accessoires = new Accessoire();

                $accessoires->setName($element[0]);
                $accessoires->setDescription($element[1]);
    
                $this->entityManager->persist($accessoires);
                $this->entityManager->flush();
             
                $io->success('Added accessoire: ' . (string)$element[0]);
            }
        }

        return Command::SUCCESS;
    }
}
