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
use SecIT\ImapBundle\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Entity\Domains;
use App\Entity\MXRecords;
use App\Entity\DMARC_Reports;
use App\Entity\DMARC_Records;
use App\Entity\DMARC_Results;
use App\Entity\SMTPTLS_Reports;
use App\Entity\SMTPTLS_Policies;
use App\Entity\SMTPTLS_MXRecords;
use App\Entity\SMTPTLS_FailureDetails;
use App\Entity\SMTPTLS_RdataRecords;
use App\Entity\Logs;

use App\Response\GetReportsResponse;
use App\EntityUnmanaged\MailReport;

use App\Enums\ReportType;

#[AsCommand(
    name: 'app:getreportsfrommailbox',
    description: 'Gets the new reports from the mailbox',
)]
class GetReportsFromMailboxCommand extends Command
{
    private $em;
    private $mailbox;
    private $params;

    public function __construct(EntityManagerInterface $em, ConnectionInterface $defaultConnection, ParameterBagInterface $params)
    {
        $this->em = $em;
        $this->mailbox = $defaultConnection;
        $this->params = $params;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->open_mailbox();

        $log = new Logs;
        $log->setTime(new \DateTime);
        $log->setSuccess($result->getSuccess());
        $log->setMessage($result->getMessage());
        $this->em->persist($log);
        $this->em->flush();


        if($result->getSuccess() == true){
            $io->success($result->getMessage());
            return Command::SUCCESS;
        } else {
            $io->error($result->getMessage());
            return Command::FAILURE;
        }
    }

    private function open_mailbox():GetReportsResponse
    {
        $response = new GetReportsResponse;

        $mailbox = $this->mailbox->getMailbox('default');
        $mail_ids = $mailbox->searchMailbox('UNSEEN','US-ASCII');

        $failed = false;
        foreach($mail_ids as $mailid) {
            $process_email_success = $this->process_email($mailbox,$mailid);

            if($process_email_success == true){
                if ($this->params->get('app.delete_processed_mails') == "true") {
                    $mailbox->deleteMail($mailid);
                }
            } else {
                //$mailbox->moveMail // https://github.com/barbushin/php-imap/blob/94107fdd1383285459a7f6c2dd2f39e25a1b8373/src/PhpImap/Mailbox.php#L757C21-L757C29
                $mailbox->setFlag(array($mailid), '\\Flagged');
                $failed = true;
            }
        }

        if($failed == true){
            $response->setSuccess(false, 'One or more reports failed to process, check flagged emails.');
        } else {
            $response->setSuccess(true, 'Mailbox processed successfully.');
        }

        return $response;
    }

    private function process_email(\PhpImap\Mailbox $mailbox, int $mailid):bool
    {
        $mail = $mailbox->getMail($mailid);
        $reports = array();
        $success = false;

        //Open archive
        try {
            $attachments = $mail->getAttachments();
            foreach ($attachments as $attachment) {
                $report = new MailReport;
                $report->setMailId($mail->headers->message_id);

                $result = $this->open_archive($attachment->filePath);
                if($result['success'] == true){
                    $report->setSuccess(true, 'Report loaded successfully.');
                    $report->setReport($result['report']);
                    $report->setReportType($result['reporttype']);
                    $success = true;
                } else {
                    $report->setSuccess(false, 'Failed to open report.');
                }
                unlink($attachment->filePath);
            }
        } catch (\Exception $e) {
            $report = new MailReport;
            $report->setReportType(ReportType::UNKNOWN);
            $report->setMailId($mail->headers->message_id);
            $report->setSuccess(false, 'Failed to open email attachment.');
        } finally {
            if (isset($report) && $report != null) {
                $reports[] = $report;
            }
        }
        
        //Process report
        $results = array();
        foreach($reports as $report){
            try {
                if(!is_null($report)){
                    if($report->getReportType() == ReportType::DMARC) {
                        $result = $this->process_dmarc_report($report);
                    } elseif($report->getReportType() == ReportType::STS) {
                        $result = $this->process_sts_report($report);
                    } else {
                        $result = false;
                    }
                } else {
                    $result = false;
                }

                if($result == false){
                    $log = new Logs;
                    $log->setTime(new \DateTime);
                    $log->setSuccess($result);
                    $log->setMessage($report->getMailId().": ".$report->getMessage());
                    $this->em->persist($log);
                    $this->em->flush();
                }
            } catch (\Exception $e) {
                $result = false;

                $log = new Logs;
                $log->setTime(new \DateTime);
                $log->setSuccess($result);
                $log->setMessage($report->getMailId().": ".$e->getMessage());
                $this->em->persist($log);
                $this->em->flush();
            } finally {
                $results[] = $result;
            }
        }

        if (in_array(false, $results)) {
            $success = false;
        } else {
            $success = true;
        }

        return $success;
    }

    private function open_archive($file): array
    {
        $report = null;
        $reporttype = ReportType::Unknown;
        $success = false;

        $ziparchive = new \ZipArchive;
        $filecontents = null;
        
        if ($ziparchive->open($file) === TRUE) {
            for($i=0; $i<$ziparchive->numFiles; $i++){
                $stat = $ziparchive->statIndex($i);
                $filecontents = file_get_contents("zip://$file#".$stat["name"]);
            }
        } elseif($gzarchive = gzopen($file, 'r')) {
            $gzcontents=null;
            while (!feof($gzarchive)) {
                $gzcontents .= gzread($gzarchive, filesize($file));
            }
            $filecontents = $gzcontents;
        }

        if(substr($filecontents, 0, 5) == "<?xml") {
            //Expecting an DMARC XML Report
            try {
                $report = new \SimpleXMLElement($filecontents);
                $reporttype = ReportType::DMARC;
                $success = true;
            }
            catch (\Exception $e) {
                $success = false;
            }
        }
        elseif($this->isJson($filecontents)) {
            //Expecting an SMTP-TLS JSON Report
            try {
                $report = json_decode($filecontents);
                $reporttype = ReportType::STS;
                $success = true;
            }
            catch (\Exception $e) {
                $success = false;
            }
        }

        return array('reporttype' => $reporttype, 'report' => $report, 'success' => $success);
    }

    private function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function process_dmarc_report(MailReport $report):bool
    {
        $dmarcreport = $report->getReport();
        try {
            $domain_repository = $this->em->getRepository(Domains::class);
            $dbdomain = $domain_repository->findOneBy(array('fqdn' => $dmarcreport->policy_published->domain->__toString()));
            if(!$dbdomain){
                $dbdomain = new Domains;
                $dbdomain->setFqdn($dmarcreport->policy_published->domain->__toString());
                $dbdomain->setStsVersion("STSv1");
                $dbdomain->setStsMode("enforce");
                $dbdomain->setStsMaxAge(86400);
                $dbdomain->setMailhost($dmarcreport->policy_published->domain->__toString());
                $this->em->persist($dbdomain);
                $this->em->flush();
            }
            $dbreport = new DMARC_Reports;
            $dbreport->setBeginTime((new \DateTime)->setTimestamp($dmarcreport->report_metadata->date_range->begin->__toString()));
            $dbreport->setEndTime((new \DateTime)->setTimestamp($dmarcreport->report_metadata->date_range->end->__toString()));
            $dbreport->setOrganisation($dmarcreport->report_metadata->org_name->__toString());
            $dbreport->setEmail($dmarcreport->report_metadata->email->__toString());
            $dbreport->setContactInfo($dmarcreport->report_metadata->extra_contact_info->__toString());
            $dbreport->setExternalId($dmarcreport->report_metadata->report_id->__toString());
            $dbreport->setDomain($dbdomain);
            $dbreport->setPolicyAdkim($dmarcreport->policy_published->adkim->__toString());
            $dbreport->setPolicyAspf($dmarcreport->policy_published->aspf->__toString());
            $dbreport->setPolicyP($dmarcreport->policy_published->p->__toString());
            $dbreport->setPolicySp($dmarcreport->policy_published->sp->__toString());
            $dbreport->setPolicyPct($dmarcreport->policy_published->pct->__toString());
            $this->em->persist($dbreport);
            $this->em->flush();
            
            foreach($dmarcreport->record as $record){
                
                $dbrecord = new DMARC_Records;
                $dbrecord->setReport($dbreport);
                $dbrecord->setSourceIp($record->row->source_ip->__toString());
                $dbrecord->setCount($record->row->count->__toString());
                $dbrecord->setPolicyDisposition(intval($record->row->policy_evaluated->disposition->__toString()));
                $dbrecord->setPolicyDkim($record->row->policy_evaluated->dkim->__toString());
                $dbrecord->setPolicySpf($record->row->policy_evaluated->spf->__toString());
                
                if(!empty($record->identifiers->envelope_to)){
                    $dbrecord->setEnvelopeTo($record->identifiers->envelope_to->__toString());
                }
                if(!empty($record->identifiers->envelope_from)){
                    $dbrecord->setEnvelopeFrom($record->identifiers->envelope_from->__toString());
                }
                if(!empty($record->identifiers->header_from)){
                    $dbrecord->setHeaderFrom($record->identifiers->header_from->__toString());
                }
                
                $this->em->persist($dbrecord);
                $this->em->flush();
                
                foreach($record->auth_results->dkim as $dkim_result){
                    $dbresult = new DMARC_Results;
                    $dbresult->setRecord($dbrecord);
                    $dbresult->setDomain($dkim_result->domain->__toString());
                    $dbresult->setType('dkim');
                    $dbresult->setResult($dkim_result->result->__toString());
                    $dbresult->setDkimSelector($dkim_result->selector->__toString());
                    $this->em->persist($dbresult);
                }

                foreach($record->auth_results->spf as $spf_result){
                    $dbresult = new DMARC_Results;
                    $dbresult->setRecord($dbrecord);
                    $dbresult->setDomain($spf_result->domain->__toString());
                    $dbresult->setType('spf');
                    $dbresult->setResult($spf_result->result->__toString());
                    $this->em->persist($dbresult);
                }
                $this->em->flush();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function process_sts_report(MailReport $report):bool
    {
        $smtptlsreport = $report->getReport();
        try {
            $dbreport = new SMTPTLS_Reports;
            
            $dbreport->setOrganisation($smtptlsreport->{'organization-name'});
            $dbreport->setContactInfo($smtptlsreport->{'contact-info'});
            $dbreport->setExternalId($smtptlsreport->{'report-id'});
            $dbreport->setBeginTime(new \DateTime($smtptlsreport->{'date-range'}->{'start-datetime'}));
            $dbreport->setEndTime(new \DateTime($smtptlsreport->{'date-range'}->{'end-datetime'}));
            
            foreach($smtptlsreport->policies as $policy){
                $domain_repository = $this->em->getRepository(Domains::class);
                $dbdomain = $domain_repository->findOneBy(array('fqdn' => $policy->policy->{'policy-domain'}));
                if(!$dbdomain){
                    $dbdomain = new Domains;
                    $dbdomain->setFqdn($policy->policy->{'policy-domain'});
                    $dbdomain->setStsVersion("STSv1");
                    $dbdomain->setStsMode("enforce");
                    $dbdomain->setStsMaxAge(86400);
                    $dbdomain->setMailhost($policy->policy->{'policy-domain'});
                    $this->em->persist($dbdomain);
                    $this->em->flush();
                }

                $dbreport->setDomain($dbdomain);
                $this->em->persist($dbreport);
            }
            $this->em->persist($dbreport);
            $this->em->flush();

            foreach($smtptlsreport->policies as $policy){
                $domain_repository = $this->em->getRepository(Domains::class);
                $dbdomain = $domain_repository->findOneBy(array('fqdn' => $policy->policy->{'policy-domain'}));
                if(!$dbdomain){
                    $dbdomain = new Domains;
                    $dbdomain->setFqdn($policy->policy->{'policy-domain'});
                    $dbdomain->setStsVersion("STSv1");
                    $dbdomain->setStsMode("enforce");
                    $dbdomain->setStsMaxAge(86400);
                    $dbdomain->setMailhost($policy->policy->{'policy-domain'});
                    $this->em->persist($dbdomain);
                    $this->em->flush();
                }
                $dbpolicy = new SMTPTLS_Policies;
                $dbpolicy->setReport($dbreport);
                $dbpolicy->setPolicyType($policy->policy->{'policy-type'});
                $dbpolicy->setPolicyDomain($dbdomain);
                $dbpolicy->setSummarySuccessfulCount($policy->summary->{'total-successful-session-count'});
                $dbpolicy->setSummaryFailedCount($policy->summary->{'total-failure-session-count'});
                $this->em->persist($dbpolicy);
                $this->em->flush();

                if($policy->policy->{'policy-type'} == 'sts' && property_exists($policy->policy, 'policy-string')){
                    if($policy_version = preg_grep('/^version:.*/', $policy->policy->{'policy-string'}) != null)
                    {
                        $dbpolicy->setPolicyStringVersion(str_replace("version: ","",array_slice($policy_version, 0, 1)[0]));
                    }
                    if($policy_mode = preg_grep('/^mode:.*/', $policy->policy->{'policy-string'}) != null)
                    {
                        $dbpolicy->setPolicyStringMode(str_replace("mode: ","",array_slice($policy_mode, 0, 1)[0]));
                    }
                    if($policy_max_age = preg_grep('/^max_age:.*/', $policy->policy->{'policy-string'}) != null)
                    {
                        $dbpolicy->setPolicyStringMaxage(str_replace("max_age: ","",array_slice($policy_max_age, 0, 1)[0]));
                    }
                    if($policy_mx = preg_grep('/^mx:.*/', $policy->policy->{'policy-string'}) != null)
                    {
                        $mxrecords=str_replace("mx: ","",array_values($policy_mx));
                    } else {
                        $mxrecords = null;
                    }
                    $this->em->persist($dbpolicy);
                    $this->em->flush();
                    
                    $i=0;
                    if($mxrecords){
                        foreach($mxrecords as $mxrecord){
                            $i++;
    
                            $mx_repository = $this->em->getRepository(MXRecords::class);
                            $dbmxrecord = $mx_repository->findOneBy(array('domain' => $dbdomain, 'name' => $mxrecord));
                            if(!$dbmxrecord){
                                $dbmxrecord = new MXRecords;
                                $dbmxrecord->setDomain($dbdomain);
                                $dbmxrecord->setName($mxrecord);
                                $dbmxrecord->setInSts(true);
                                $this->em->persist($dbmxrecord);
                                $this->em->flush();
                            }
    
                            $dbmx = new SMTPTLS_MXRecords;
                            $dbmx->setMXRecord($dbmxrecord);
                            $dbmx->setPolicy($dbpolicy);
                            $dbmx->setPriority($i);
                            $this->em->persist($dbmx);
                            $this->em->flush();
                        }
                    }
                }
                elseif($policy->policy->{'policy-type'} == 'tlsa' && property_exists($policy->policy, 'policy-string')){
                    foreach($policy->policy->{'policy-string'} as $rdatarecord){
                        preg_match('/([0-9])\s([0-9])\s([0-9])\s([0-9A-Za-z]+)/', $rdatarecord, $rdatarow);

                        $rdata = new SMTPTLS_RdataRecords;
                        $rdata->setPolicy($dbpolicy);
                        $rdata->setUsagetype($rdatarow[1]);
                        $rdata->setSelectortype($rdatarow[2]);
                        $rdata->setMatchingtype($rdatarow[3]);
                        $rdata->setData($rdatarow[4]);
                        $this->em->persist($rdata);
                        $this->em->flush();
                    }
                }

                if(property_exists($policy, 'failure-details')){
                    foreach($policy->{'failure-details'} as $failure){
                        $mx_repository = $this->em->getRepository(MXRecords::class);
                        $dbmxrecord = $mx_repository->findOneBy(array('domain' => $dbdomain, 'name' => $failure->{'receiving-mx-hostname'}));
                        if(!$dbmxrecord){
                            $dbmxrecord = new MXRecords;
                            $dbmxrecord->setDomain($dbdomain);
                            $dbmxrecord->setName($failure->{'receiving-mx-hostname'});
                            $dbmxrecord->setInSts(true);
                            $this->em->persist($dbmxrecord);
                            $this->em->flush();
                        }

                        $dbfailure = new SMTPTLS_FailureDetails;
                        $dbfailure->setPolicy($dbpolicy);
                        $dbfailure->setResultType($failure->{'result-type'});
                        $dbfailure->setSendingMtaIp($failure->{'sending-mta-ip'});
                        if(property_exists($failure, 'receiving-ip')){
                            $dbfailure->setReceivingIp($failure->{'receiving-ip'});
                        }
                        if($dbmxrecord){
                            $dbfailure->setReceivingMxHostname($dbmxrecord);
                        }
                        $dbfailure->setFailedSessionCount($failure->{'failed-session-count'});
                        $this->em->persist($dbfailure);
                        $this->em->flush();
                    }
                }
            }
            return true;
            }
        catch (\Exception $e) {
            return false;
        }
    }
}

