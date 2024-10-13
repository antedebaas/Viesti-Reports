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

use App\Enums\StateType;
use App\Enums\ReportType;

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
        $lock = $repository->findOneBy(array('name' => 'check_mailbox_lock'));
        if($lock){
            $lock->setValue('0');
            $this->em->persist($lock);
        }

        $details = array(
            'count' => 1,
            'reports' => array(
                array(
                    'type' => ReportType::Other,
                    'state' => StateType::Good,
                    'message' => 'Mail lock has been removed manually'
                )
            )
        );

        if (!$input->getOption('quiet')) {
            $log = new Logs();
            $log->setTime(new \DateTime());
            $log->setState(StateType::Good);
            $log->setMessage("Lock manually removed");
            $log->setDetails($details);
            $log->setMailcount(0);
            $this->em->persist($log);
            $this->em->flush();
            $io->success('Lock manually removed');
        }
        $this->em->flush();
        

        return Command::SUCCESS;
    }
}
