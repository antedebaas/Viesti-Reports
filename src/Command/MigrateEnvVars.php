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

use Symfony\Component\Dotenv\Dotenv;

use App\Enums\StateType;
use App\Enums\ReportType;

#[AsCommand(
    name: 'app:migrateenvvars',
    description: 'Remove mail locks',
)]
class MigrateEnvVars extends Command
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
        $migrated = false;

        $repository = $this->em->getRepository(Config::class);
        $fileContents = file_get_contents(__DIR__.'/../../.env.local');
        $lines = preg_split('/(\n|\r\n)/', $fileContents);

        foreach ($lines as $line) {
            preg_match('/^([A-Z_]+)=(.*)/', $line, $matches);
            if(count($matches) > 0){
                if($matches[2][0] == '"' && $matches[2][strlen($matches[2])-1] == '"'){
                    $matches[2] = substr($matches[2], 1, strlen($matches[2])-2);
                }
                switch ($matches[1]){
                    case 'DELETE_PROCESSED_MAILS':
                        $config = $repository->getKey('delete_processed_mails');
                        if(!$config){
                            $config = new Config();
                            $config->setName('delete_processed_mails');
                            $config->setValue($matches[2]); 
                            $config->setType('boolean');
                            $this->em->persist($config);
                            $this->em->flush();
                            $migrated = true;
                        }
                        break;
                    case 'ENABLE_REGISTRATION':
                        $config = $repository->getKey('enable_registration');
                        if(!$config){
                            $config = new Config();
                            $config->setName('enable_registration');
                            $config->setValue($matches[2]); 
                            $config->setType('boolean');
                            $this->em->persist($config);
                            $this->em->flush();
                            $migrated = true;
                        }
                        break;
                    case 'PUSHOVER_API_KEY':
                        $config_pushover = $repository->getKey('enable_pushover');
                        if(!$config_pushover){
                            $config_pushover = new Config();
                            $config_pushover->setName('enable_pushover');
                            $config_pushover->setValue(0);
                            $config_pushover->setType('boolean');
                            $this->em->persist($config_pushover);
                            $this->em->flush();
                            $migrated = true;
                        }
                        $config_apikey = $repository->getKey('pushover_api_key');
                        if(!$config_apikey){
                            $config_apikey = new Config();
                            $config_apikey->setName('pushover_api_key');
                            $config_apikey->setValue(""); 
                            $config_apikey->setType('string');
                            if($matches[2] != ''){
                                $config_pushover->setValue(1);
                                $config_apikey->setValue($matches[2]); 
                            }
                            $this->em->persist($config_apikey);
                            $this->em->flush();
                            $migrated = true;
                        }
                        break;
                    case 'PUSHOVER_USER_KEY':
                        $config_userkey = $repository->getKey('pushover_user_key');
                        if(!$config_userkey){
                            $config_userkey = new Config();
                            $config_userkey->setName('pushover_user_key');
                            $config_userkey->setValue(""); 
                            $config_userkey->setType('string');
                            if($matches[2] != ''){
                                $config_userkey->setValue($matches[2]); 
                            }
                            $this->em->persist($config_userkey);
                            $this->em->flush();
                            $migrated = true;
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        $details = array(
            'count' => 1,
            'reports' => array(
                array(
                    'type' => ReportType::Other,
                    'state' => StateType::Good,
                    'message' => 'Env vars migrated to config table'
                )
            )
        );

        if (!$input->getOption('quiet') && $migrated == true) {
            $log = new Logs();
            $log->setTime(new \DateTime());
            $log->setState(StateType::Good);
            $log->setMessage("Env vars migrated to config table");
            $log->setDetails($details);
            $log->setMailcount(0);
            $this->em->persist($log);
            $this->em->flush();
            $io->success('Env vars migrated to config table');
        }

        return Command::SUCCESS;
    }
}
