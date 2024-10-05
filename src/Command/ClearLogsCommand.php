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
use App\Enums\StateType;
use App\Enums\ReportType;

#[AsCommand(
    name: 'app:clearlogs',
    description: 'Clears the logs',
)]
class ClearLogsCommand extends Command
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

        $repository = $this->em->getRepository(Logs::class);
        $logs = $repository->findAll();
        foreach ($logs as $log) {
            $this->em->remove($log);
        }

        $details = array(
            'count' => 1,
            'reports' => array(
                array(
                    'type' => ReportType::Other,
                    'state' => StateType::Good,
                    'message' => 'Logs have been cleared manually'
                )
            )
        );

        $log = new Logs();
        $log->setTime(new \DateTime());
        $log->setState(StateType::Good);
        $log->setMessage("Logs cleared");
        $log->setDetails($details);
        $log->setMailcount(0);
        $this->em->persist($log);
        $this->em->flush();

        $io->success('Logs have been cleared');

        return Command::SUCCESS;
    }
}
