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
use SecIT\ImapBundle\Service\Imap;

use App\Entity\Domains;
use App\Entity\Reports;
use App\Entity\Records;
use App\Entity\Results;
use App\Entity\Logs;

#[AsCommand(
    name: 'app:checkmailbox',
    description: 'Add a short description for your command',
)]
class CheckmailboxCommand extends Command
{
    private $em;
    private $imap;

    public function __construct(EntityManagerInterface $em, Imap $imap)
    {
        $this->em = $em;
        $this->imap = $imap;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $stats=array(
            'new_emails' => 0,
            'new_domains' => 0,
            'new_reports' => 0,
            'new_records' => 0,
            'new_results' => 0,
        );

        $mailresult = $this->open_mailbox($this->imap);
        $stats['new_emails'] = $mailresult['num_emails'];
        
        foreach($mailresult['reports'] as $report){
            $stats['new_reports']++;

            $domain_repository = $this->em->getRepository(Domains::class);
            $dbdomain = $domain_repository->findOneBy(array('fqdn' => $report->policy_published->domain->__toString()));
            if(!$dbdomain){
                $stats['new_domains']++;

                $dbdomain = new Domains;
                $dbdomain->setFqdn($report->policy_published->domain->__toString());
                $this->em->persist($dbdomain);
                $this->em->flush();
            }
            $dbreport = new Reports;
            $dbreport->setBeginTime((new \DateTime)->setTimestamp($report->report_metadata->date_range->begin->__toString()));
            $dbreport->setEndTime((new \DateTime)->setTimestamp($report->report_metadata->date_range->end->__toString()));
            $dbreport->setOrganisation($report->report_metadata->org_name->__toString());
            $dbreport->setEmail($report->report_metadata->email->__toString());
            $dbreport->setContactInfo($report->report_metadata->extra_contact_info->__toString());
            $dbreport->setExternalId($report->report_metadata->report_id->__toString());
            $dbreport->setDomain($dbdomain);
            $dbreport->setPolicyAdkim($report->policy_published->adkim->__toString());
            $dbreport->setPolicyAspf($report->policy_published->aspf->__toString());
            $dbreport->setPolicyP($report->policy_published->p->__toString());
            $dbreport->setPolicySp($report->policy_published->sp->__toString());
            $dbreport->setPolicyPct($report->policy_published->pct->__toString());
            $this->em->persist($dbreport);
            $this->em->flush();
            
            foreach($report->record as $record){
                $stats['new_records']++;

                $dbrecord = new Records;
                $dbrecord->setReport($dbreport);
                $dbrecord->setSourceIp($record->row->source_ip->__toString());
                $dbrecord->setCount($record->row->count->__toString());
                $dbrecord->setPolicyDisposition(intval($record->row->policy_evaluated->disposition->__toString()));
                $dbrecord->setPolicyDkim($record->row->policy_evaluated->dkim->__toString());
                $dbrecord->setPolicySpf($record->row->policy_evaluated->spf->__toString());
                $dbrecord->setEnvelopeTo($record->identifiers->envelope_to->__toString());
                $dbrecord->setEnvelopeFrom($record->identifiers->envelope_from->__toString());
                $dbrecord->setHeaderFrom($record->identifiers->header_from->__toString());
                $this->em->persist($dbrecord);
                $this->em->flush();
                
                foreach($record->auth_results->dkim as $dkim_result){
                    $stats['new_results']++;

                    $dbresult = new Results;
                    $dbresult->setRecord($dbrecord);
                    $dbresult->setDomain($dkim_result->domain->__toString());
                    $dbresult->setType('dkim');
                    $dbresult->setResult($dkim_result->result->__toString());
                    $dbresult->setDkimSelector($dkim_result->selector->__toString());
                    $this->em->persist($dbresult);
                }

                foreach($record->auth_results->spf as $spf_result){
                    $stats['new_results']++;

                    $dbresult = new Results;
                    $dbresult->setRecord($dbrecord);
                    $dbresult->setDomain($spf_result->domain->__toString());
                    $dbresult->setType('spf');
                    $dbresult->setResult($spf_result->result->__toString());
                    $this->em->persist($dbresult);
                }
                $this->em->flush();
            }
        }

        $log = new Logs;
        $log->setTime(new \DateTime);
        $log->setMessage('Mailbox checked: '.$stats['new_emails'].' new emails, '.$stats['new_domains'].' new domains, '.$stats['new_reports'].' new reports, '.$stats['new_records'].' new records, '.$stats['new_results'].' new results.');
        $this->em->persist($log);
        $this->em->flush();

        $io->success('Mailbox checked: '.$stats['new_emails'].' new emails, '.$stats['new_domains'].' new domains, '.$stats['new_reports'].' new reports, '.$stats['new_records'].' new records, '.$stats['new_results'].' new results.');

        return Command::SUCCESS;
    }

    private function open_mailbox(Imap $imap):array
    {
        $num_emails=0;
        $mailbox = $imap->get('default');
        $mailsIds = $mailbox->searchMailbox('UNSEEN');
        $reports=array();
        foreach($mailsIds as $mailId) {
            $num_emails++;
            $mail = $mailbox->getMail($mailId);
            $attachments = $mail->getAttachments();
            foreach ($attachments as $attachment) {
                $reports = array_merge($reports, $this->open_archive($attachment->filePath));
                unlink($attachment->filePath);
            }
        }
        return array('num_emails' => $num_emails, 'reports' => $reports);
    }

    private function open_archive($file): array
    {
        $reports = array();
        $ziparchive = new \ZipArchive;
        
        if ($ziparchive->open($file) === TRUE) {
            for($i=0; $i<$ziparchive->numFiles; $i++){
                $stat = $ziparchive->statIndex($i);
                $reports[] = new \SimpleXMLElement(file_get_contents("zip://$file#".$stat["name"]));
            }
        } elseif($gzarchive = gzopen($file, 'r')) {
            $gzcontents=null;
            while (!feof($gzarchive)) {
                $gzcontents .= gzread($gzarchive, filesize($file));
            }
            $reports[] = new \SimpleXMLElement($gzcontents);
        }
        return $reports;
    }
}
