<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Logs;
use App\Entity\Config;

#[AsCommand(
    name: 'app:removemaillock',
    description: 'Remove mail locks',
)]
class RemoveMailLock extends Command
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $repository = $this->em->getRepository(Config::class);
        $lock = $repository->findOneBy(array('name' => 'getreportsfrommailbox.lock'));
        $lock->setValue('false');
        $this->em->persist($lock);

        $log = new Logs();
        $log->setTime(new \DateTime());
        $log->setSuccess(true);
        $log->setMessage("Lock manually removed");
        $this->em->persist($log);

        $this->em->flush();

        $io->success('Lock manually removed');

        return Command::SUCCESS;
    }
}
