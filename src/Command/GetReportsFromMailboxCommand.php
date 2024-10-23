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
use App\Entity\Config;

use App\Response\MailboxResponse;
use App\Response\MailReportResponse;

use Serhiy\Pushover\Application;
use Serhiy\Pushover\Recipient;
use Serhiy\Pushover\Api\Message\Message;
use Serhiy\Pushover\Api\Message\Notification;

use App\Enums\ReportType;
use App\Enums\StateType;

#[AsCommand(
    name: 'app:getreportsfrommailbox',
    description: 'Gets the new reports from the mailbox',
)]
class GetReportsFromMailboxCommand extends Command
{
    private $em;
    private $mailbox;
    private $mailbox_secondary;
    private $params;

    public function __construct(EntityManagerInterface $em, ConnectionInterface $defaultConnection, ConnectionInterface $secondaryConnection, ParameterBagInterface $params)
    {
        $this->em = $em;
        $this->mailbox = $defaultConnection;
        $this->mailbox_secondary = $secondaryConnection;
        $this->params = $params;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $repository = $this->em->getRepository(Config::class);
        $lock = $repository->getKey('check_mailbox_lock');
        if(!$lock) {
            $lock = new Config();
            $lock->setName('check_mailbox_lock');
            $lock->setValue('0');
            $lock->setType('boolean');
            $this->em->persist($lock);
            $this->em->flush();
        }
        
        try {
            if($lock->getValue() == '1') {
                $log = new Logs();
                $log->setTime(new \DateTime());
                $log->setState(StateType::Warn);
                $log->setMessage("GetReportsFromMailbox command was already running.");
                $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Warn, 'message' => "GetReportsFromMailbox command was already running."))));
                $log->setMailcount(0);
                $this->em->persist($log);
                $this->em->flush();

                $io->error('GetReportsFromMailbox command is already running.');
                return Command::FAILURE;
            } else {
                $lock->setValue('1');
                $this->em->persist($lock);
                $this->em->flush();

                $results['primary'] = $this->open_mailbox($this->mailbox);
                if($this->mailbox_secondary->isEnabled()) {
                    $results['secondary'] = $this->open_mailbox($this->mailbox_secondary);
                } else {
                    $results['secondary'] = new MailboxResponse();
                    $results['secondary']->setState(StateType::Warn, 'Secondary mailbox is disabled.', array('count' => 0, 'reports' => array()));
                }

                if(!empty($this->params->get('app.pushover_api_key')) && !empty($this->params->get('app.pushover_user_key')))
                {
                    $count = $results['primary']->getDetails()["count"] + $results['secondary']->getDetails()["count"];
                    if($count > 0) {
                        $application = new Application($this->params->get('app.pushover_api_key'));
                        $recipient = new Recipient($this->params->get('app.pushover_user_key'));
                        $message = new Message($count.' new emails have been processed by viesti reports', 'New reports processed.');
                        $notification = new Notification($application, $recipient, $message);
                        $notification->push();
                    }
                }

                $log = new Logs();
                $log->setTime(new \DateTime());
                $log->setState($results['primary']->getState());
                $log->setMessage($results['primary']->getMessage());
                foreach ($results['primary']->getDetails()["reports"] as $report) {
                    $report->setReport(null);
                }
                $log->setDetails($results['primary']->getDetails());
                $log->setMailcount($results['primary']->getDetails()["count"]);
                $this->em->persist($log);
                $this->em->flush();

                if($this->mailbox_secondary->isEnabled()) {
                    $log = new Logs();
                    $log->setTime(new \DateTime());
                    $log->setState($results['secondary']->getState());
                    $log->setMessage($results['secondary']->getMessage());
    
                    foreach ($results['secondary']->getDetails()["reports"] as $report) {
                        $report->setReport(null);
                    }
                    $log->setDetails($results['primary']->getDetails());
                    $log->setMailcount($results['primary']->getDetails()["count"]);
                    $this->em->persist($log);
                    $this->em->flush();
                }

                $lock->setValue('0');
                $this->em->persist($lock);
                $this->em->flush();
        
                if($results['primary']->getState() == true && $results['secondary']->getState() == true) {
                    $io->success($results['primary']->getMessage());
                    if($this->mailbox_secondary->isEnabled()) {
                        $io->success($results['secondary']->getMessage());
                    }
                    return Command::SUCCESS;
                } else {
                    if($results['primary']->getState() == false) {
                        $io->error($results['primary']->getMessage());
                    }
                    if($results['secondary']->getState() == false) {
                        $io->error($results['secondary']->getMessage());
                    }
                    return Command::FAILURE;
                }
            }
        } catch (\Exception $e) {
            $log = new Logs();
            $log->setTime(new \DateTime());
            $log->setState(StateType::Fail);
            $log->setMessage("Exception in GetReportsFromMailbox command");
            $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $e->getMessage()))));
            $log->setMailcount(0);
            $this->em->persist($log);
            $lock->setValue('0');
            $this->em->persist($lock);
            $this->em->flush();

            $io->error($e->getMessage());
            return Command::FAILURE;
        } catch (\Error $e) {
            $log = new Logs();
            $log->setTime(new \DateTime());
            $log->setState(StateType::Fail);
            $log->setMessage("Error in GetReportsFromMailbox command");
            $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $e->getMessage()))));
            $log->setMailcount(0);
            $this->em->persist($log);
            $lock->setValue('0');
            $this->em->persist($lock);
            $this->em->flush();

            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }

    private function open_mailbox(ConnectionInterface $ci_mailbox): MailboxResponse
    {
        $response = new MailboxResponse();

        $repository = $this->em->getRepository(Config::class);
        $lock = $repository->findOneBy(array('name' => 'check_mailbox_lock'));

        $mailbox = $ci_mailbox->getMailbox();
        $mail_ids = $mailbox->searchMailbox('UNSEEN');
        $details = array('count' => 0, 'reports' => array());

        $success = false;
        foreach($mail_ids as $mailid) {

            $result = $this->process_email($mailbox, $mailid);

            if($result['success'] == true) {
                if ($this->params->get('app.delete_processed_mails') == "true") {
                    $mailbox->deleteMail($mailid);
                }
                $success = true;
            } else {
                //$mailbox->moveMail // https://github.com/barbushin/php-imap/blob/94107fdd1383285459a7f6c2dd2f39e25a1b8373/src/PhpImap/Mailbox.php#L757C21-L757C29
                $mailbox->setFlag(array($mailid), '\\Flagged');
            }
            $details['reports'] = array_merge($details['reports'], $result['reports']);
            $details['count']++;
        }

        if($success == false && $details['count'] > 0) {
            $failedReports = 0;
            foreach ($details['reports'] as $report) {
                if($report->getState() == StateType::Fail) {
                    $failedReports++;
                }
              }

            $response->setState(StateType::Fail, $failedReports.' out of '.$details['count'].' emails failed to process, check flagged emails.', $details);
        } else {
            $response->setState(StateType::Good, 'Processed '.$details['count'].' emails successfully.', $details);
        }

        return $response;
    }

    private function process_email(\PhpImap\Mailbox $mailbox, int $mailid): array
    {
        $repository = $this->em->getRepository(Config::class);
        $lock = $repository->findOneBy(array('name' => 'check_mailbox_lock'));

        $mail = $mailbox->getMail($mailid);
        $reports = array();
        $response = array('success' => false, 'reports' => array());

        //Open archive
        try {
            $attachments = $mail->getAttachments();
            if (empty($attachments)) {
                $report = new MailReportResponse();
                $report->setType(ReportType::Other);
                $report->setMailId($mail->headers->message_id ?? "mail-id-".$mailid);
                $report->setState(StateType::Fail, 'Email does not have any attachment.');
            } else {
                foreach ($attachments as $attachment) {
                    $report = new MailReportResponse();
                    $report->setMailId($mail->headers->message_id ?? "mail-id-".$mailid);
    
                    $result = $this->open_archive($attachment->filePath);
                    if($result['success'] == true) {
                        $report->setState(StateType::Good, 'Report loaded successfully.');
                        $report->setType($result['type']);
                        $report->setReport($result['report']);
                        $response['reports'][] = $report;
                    } else {
                        $report->setState(StateType::Fail, 'Failed to open report.');
                    }
                    unlink($attachment->filePath);
                }
            }
        } catch (\Exception $e) {
            $report = new MailReportResponse();
            $report->setType(ReportType::Other);
            $report->setMailId($mail->headers->message_id ?? "mail-id-".$mailid);
            $report->setState(StateType::Fail, 'Failed to open email attachment.');
            $response['reports'][] = $report;
        } catch (\Error $e) {
            $report = new MailReportResponse();
            $report->setType(ReportType::Other);
            $report->setMailId($mail->headers->message_id ?? "mail-id-".$mailid);
            $report->setState(StateType::Fail, $e->getMessage());
            $response['reports'][] = $report;
        }

        //Process report
        $results = array();
        foreach($response['reports'] as $report) {
            try {
                if(!is_null($report)) {
                    if($report->getType() == ReportType::DMARC) {
                        $result = $this->process_dmarc_report($report);
                    } elseif($report->getType() == ReportType::STS) {
                        $result = $this->process_sts_report($report);
                    } else {
                        $result = false;
                    }

                    if($result == false) {
                        $report->setSuccess(false);
                    }
                } else {
                    $result = false;
                }

                if($result == false) {
                    $log = new Logs();
                    $log->setTime(new \DateTime());
                    if($result == false) {
                        $log->setState(StateType::Fail);
                    } else {
                        $log->setState(StateType::Good);
                    }
                    $log->setMessage($report->getMailId());
                    $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $report->getMessage()))));
                    $log->setMailcount(0);
                    $this->em->persist($log);
                    $this->em->flush();
                }
            } catch (\Exception $e) {
                $result = false;

                $log = new Logs();
                $log->setTime(new \DateTime());
                $log->setState(StateType::Fail);
                $log->setMessage("Exception while processing mailid: ".$report->getMailId());
                $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $e->getMessage()))));
                $log->setMailcount(1);
                $this->em->persist($log);
                $lock->setValue('0');
                $this->em->persist($lock);
                $this->em->flush();
            }  catch (\Error $e) {
                $result = false;

                $log = new Logs();
                $log->setTime(new \DateTime());
                $log->setState(StateType::Fail);
                $log->setMessage("Error while processing mailid: ".$report->getMailId());
                $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $e->getMessage()))));
                $log->setMailcount(1);
                $this->em->persist($log);
                $lock->setValue('0');
                $this->em->persist($lock);
                $this->em->flush();
            } finally {
                $results[] = $result;
            }
        }

        if (in_array(false, $results)) {
            $response['success'] = false;
        } else {
            $response['success'] = true;
        }

        return $response;
    }

    private function open_archive($file): array
    {
        $repository = $this->em->getRepository(Config::class);
        $lock = $repository->findOneBy(array('name' => 'check_mailbox_lock'));

        $report = null;
        $reporttype = ReportType::Other;
        $success = false;

        try {
            $ziparchive = new \ZipArchive();
            $filecontents = null;

            if ($ziparchive->open($file) === true) {
                for($i = 0; $i < $ziparchive->numFiles; $i++) {
                    $stat = $ziparchive->statIndex($i);
                    $filecontents = file_get_contents("zip://$file#".$stat["name"]);
                }
                $ziparchive->close();
            } elseif($gzarchive = gzopen($file, 'r')) {
                $gzcontents = null;
                while (!feof($gzarchive)) {
                    $gzcontents .= gzread($gzarchive, filesize($file));
                }
                $filecontents = $gzcontents;
                gzclose($gzarchive);
            }
        } catch(\Exception $e) {
            $log = new Logs();
            $log->setTime(new \DateTime());
            $log->setState(StateType::Fail);
            $log->setMessage("Exception while while operning archive");
            $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $e->getMessage()))));
            $log->setMailcount(1);
            $this->em->persist($log);
            $lock->setValue('0');
            $this->em->persist($lock);
            $this->em->flush();
        } catch(\Error $e) {
            $log = new Logs();
            $log->setTime(new \DateTime());
            $log->setState(StateType::Fail);
            $log->setMessage("Error while while operning archive");
            $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $e->getMessage()))));
            $log->setMailcount(1);
            $this->em->persist($log);
            $lock->setValue('0');
            $this->em->persist($lock);
            $this->em->flush();
        }

        if(substr($filecontents, 0, 5) == "<?xml") {
            //Expecting an DMARC XML Report
            try {
                $report = new \SimpleXMLElement($filecontents);
                $reporttype = ReportType::DMARC;
                $success = true;
            } catch (\Exception $e) {
                $log = new Logs();
                $log->setTime(new \DateTime());
                $log->setState(StateType::Fail);
                $log->setMessage("Failed to open DMARC report");
                $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $e->getMessage()))));
                $log->setMailcount(1);
                $this->em->persist($log);
                $lock->setValue('0s');
                $this->em->persist($lock);
                $this->em->flush();
                $success = false;
            }
        } elseif($this->isJson($filecontents)) {
            //Expecting an SMTP-TLS JSON Report
            try {
                $report = json_decode($filecontents);
                $reporttype = ReportType::STS;
                $success = true;
            } catch (\Exception $e) {
                $log = new Logs();
                $log->setTime(new \DateTime());
                $log->setState(StateType::Fail);
                $log->setMessage("Failed to open MTA-STS report");
                $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $e->getMessage()))));
                $log->setMailcount(1);
                $this->em->persist($log);
                $lock->setValue('0');
                $this->em->persist($lock);
                $this->em->flush();
                $success = false;
            }
        }

        return array('type' => $reporttype, 'report' => $report, 'success' => $success);
    }

    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function process_dmarc_report(MailReportResponse $report): bool
    {
        $repository = $this->em->getRepository(Config::class);
        $lock = $repository->findOneBy(array('name' => 'check_mailbox_lock'));

        $dmarcreport = $report->getReport();
        try {
            $domain_repository = $this->em->getRepository(Domains::class);
            $dbdomain = $domain_repository->findOneBy(array('fqdn' => $dmarcreport->policy_published->domain->__toString()));
            if(!$dbdomain) {
                $dbdomain = new Domains();
                $dbdomain->setFqdn($dmarcreport->policy_published->domain->__toString());
                $dbdomain->setStsVersion("STSv1");
                $dbdomain->setStsMode("enforce");
                $dbdomain->setStsMaxAge(86400);
                $dbdomain->setMailhost($dmarcreport->policy_published->domain->__toString());
                $this->em->persist($dbdomain);
                $this->em->flush();
            }
            $dbreport = new DMARC_Reports();
            $dbreport->setBeginTime((new \DateTime())->setTimestamp($dmarcreport->report_metadata->date_range->begin->__toString()));
            $dbreport->setEndTime((new \DateTime())->setTimestamp($dmarcreport->report_metadata->date_range->end->__toString()));
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

            foreach($dmarcreport->record as $record) {

                $dbrecord = new DMARC_Records();
                $dbrecord->setReport($dbreport);
                $dbrecord->setSourceIp($record->row->source_ip->__toString());
                $dbrecord->setCount($record->row->count->__toString());
                $dbrecord->setPolicyDisposition(intval($record->row->policy_evaluated->disposition->__toString()));
                $dbrecord->setPolicyDkim($record->row->policy_evaluated->dkim->__toString());
                $dbrecord->setPolicySpf($record->row->policy_evaluated->spf->__toString());

                if(!empty($record->identifiers->envelope_to)) {
                    $dbrecord->setEnvelopeTo($record->identifiers->envelope_to->__toString());
                }
                if(!empty($record->identifiers->envelope_from)) {
                    $dbrecord->setEnvelopeFrom($record->identifiers->envelope_from->__toString());
                }
                if(!empty($record->identifiers->header_from)) {
                    $dbrecord->setHeaderFrom($record->identifiers->header_from->__toString());
                }

                $this->em->persist($dbrecord);
                $this->em->flush();

                foreach($record->auth_results->dkim as $dkim_result) {
                    $dbresult = new DMARC_Results();
                    $dbresult->setRecord($dbrecord);
                    $dbresult->setDomain($dkim_result->domain->__toString());
                    $dbresult->setType('dkim');
                    $dbresult->setResult($dkim_result->result->__toString());
                    $dbresult->setDkimSelector($dkim_result->selector->__toString());
                    $this->em->persist($dbresult);
                }

                foreach($record->auth_results->spf as $spf_result) {
                    $dbresult = new DMARC_Results();
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
            $log = new Logs();
            $log->setTime(new \DateTime());
            $log->setState(StateType::Fail);
            $log->setMessage("Exception while procesing DMARC report");
            $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $e->getMessage()))));
            $log->setMailcount(1);
            $this->em->persist($log);
            $lock->setValue('0');
            $this->em->persist($lock);
            $this->em->flush();
            return false;
        } catch (\Error $e) {
            $log = new Logs();
            $log->setTime(new \DateTime());
            $log->setState(StateType::Fail);
            $log->setMessage("Error while procesing DMARC report");
            $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $e->getMessage()))));
            $log->setMailcount(1);
            $this->em->persist($log);
            $lock->setValue('0');
            $this->em->persist($lock);
            $this->em->flush();
            return false;
        }
    }

    private function process_sts_report(MailReportResponse $report): bool
    {
        $repository = $this->em->getRepository(Config::class);
        $lock = $repository->findOneBy(array('name' => 'check_mailbox_lock'));

        $smtptlsreport = $report->getReport();
        try {
            $dbreport = new SMTPTLS_Reports();

            $dbreport->setOrganisation($smtptlsreport->{'organization-name'});
            $dbreport->setContactInfo($smtptlsreport->{'contact-info'});
            $dbreport->setExternalId($smtptlsreport->{'report-id'});
            $dbreport->setBeginTime(new \DateTime($smtptlsreport->{'date-range'}->{'start-datetime'}));
            $dbreport->setEndTime(new \DateTime($smtptlsreport->{'date-range'}->{'end-datetime'}));

            foreach($smtptlsreport->policies as $policy) {
                $domain_repository = $this->em->getRepository(Domains::class);
                $dbdomain = $domain_repository->findOneBy(array('fqdn' => $policy->policy->{'policy-domain'}));
                if(!$dbdomain) {
                    $dbdomain = new Domains();
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

            foreach($smtptlsreport->policies as $policy) {
                $domain_repository = $this->em->getRepository(Domains::class);
                $dbdomain = $domain_repository->findOneBy(array('fqdn' => $policy->policy->{'policy-domain'}));
                if(!$dbdomain) {
                    $dbdomain = new Domains();
                    $dbdomain->setFqdn($policy->policy->{'policy-domain'});
                    $dbdomain->setStsVersion("STSv1");
                    $dbdomain->setStsMode("enforce");
                    $dbdomain->setStsMaxAge(86400);
                    $dbdomain->setMailhost($policy->policy->{'policy-domain'});
                    $this->em->persist($dbdomain);
                    $this->em->flush();
                }
                $dbpolicy = new SMTPTLS_Policies();
                $dbpolicy->setReport($dbreport);
                $dbpolicy->setPolicyType($policy->policy->{'policy-type'});
                $dbpolicy->setPolicyDomain($dbdomain);
                $dbpolicy->setSummarySuccessfulCount($policy->summary->{'total-successful-session-count'});
                $dbpolicy->setSummaryFailedCount($policy->summary->{'total-failure-session-count'});
                $this->em->persist($dbpolicy);
                $this->em->flush();

                if($policy->policy->{'policy-type'} == 'sts' && property_exists($policy->policy, 'policy-string')) {
                    if(preg_grep('/^version:.*/', $policy->policy->{'policy-string'}) != null) {
                        $dbpolicy->setPolicyStringVersion(str_replace("version: ", "", array_slice(preg_grep('/^version:.*/', $policy->policy->{'policy-string'}), 0, 1)[0]));
                    }
                    if(preg_grep('/^mode:.*/', $policy->policy->{'policy-string'}) != null) {
                        $dbpolicy->setPolicyStringMode(str_replace("mode: ", "", array_slice(preg_grep('/^mode:.*/', $policy->policy->{'policy-string'}), 0, 1)[0]));
                    }
                    if(preg_grep('/^max_age:.*/', $policy->policy->{'policy-string'}) != null) {
                        $dbpolicy->setPolicyStringMaxage(str_replace("max_age: ", "", array_slice(preg_grep('/^max_age:.*/', $policy->policy->{'policy-string'}), 0, 1)[0]));
                    }
                    if(preg_grep('/^mx:.*/', $policy->policy->{'policy-string'}) != null) {
                        $mxrecords = str_replace("mx: ", "", array_values(preg_grep('/^mx:.*/', $policy->policy->{'policy-string'})));
                    } else {
                        $mxrecords = null;
                    }
                    $this->em->persist($dbpolicy);
                    $this->em->flush();

                    $i = 0;
                    if($mxrecords) {
                        foreach($mxrecords as $mxrecord) {
                            $i++;

                            $mx_repository = $this->em->getRepository(MXRecords::class);
                            $dbmxrecord = $mx_repository->findOneBy(array('domain' => $dbdomain, 'name' => $mxrecord));
                            if(!$dbmxrecord) {
                                $dbmxrecord = new MXRecords();
                                $dbmxrecord->setDomain($dbdomain);
                                $dbmxrecord->setName($mxrecord);
                                $dbmxrecord->setInSts(true);
                                $this->em->persist($dbmxrecord);
                                $this->em->flush();
                            }

                            $dbmx = new SMTPTLS_MXRecords();
                            $dbmx->setMXRecord($dbmxrecord);
                            $dbmx->setPolicy($dbpolicy);
                            $dbmx->setPriority($i);
                            $this->em->persist($dbmx);
                            $this->em->flush();
                        }
                    }
                } elseif($policy->policy->{'policy-type'} == 'tlsa' && property_exists($policy->policy, 'policy-string')) {
                    foreach($policy->policy->{'policy-string'} as $rdatarecord) {
                        preg_match('/([0-9])\s([0-9])\s([0-9])\s([0-9A-Za-z]+)/', $rdatarecord, $rdatarow);

                        $rdata = new SMTPTLS_RdataRecords();
                        $rdata->setPolicy($dbpolicy);
                        $rdata->setUsagetype($rdatarow[1]);
                        $rdata->setSelectortype($rdatarow[2]);
                        $rdata->setMatchingtype($rdatarow[3]);
                        $rdata->setData($rdatarow[4]);
                        $this->em->persist($rdata);
                        $this->em->flush();
                    }
                }

                if(property_exists($policy, 'failure-details')) {
                    foreach($policy->{'failure-details'} as $failure) {
                        $mx_repository = $this->em->getRepository(MXRecords::class);
                        $dbmxrecord = $mx_repository->findOneBy(array('domain' => $dbdomain, 'name' => $failure->{'receiving-mx-hostname'}));
                        if(!$dbmxrecord) {
                            $dbmxrecord = new MXRecords();
                            $dbmxrecord->setDomain($dbdomain);
                            $dbmxrecord->setName($failure->{'receiving-mx-hostname'});
                            $dbmxrecord->setInSts(true);
                            $this->em->persist($dbmxrecord);
                            $this->em->flush();
                        }

                        $dbfailure = new SMTPTLS_FailureDetails();
                        $dbfailure->setPolicy($dbpolicy);
                        $dbfailure->setResultType($failure->{'result-type'});
                        $dbfailure->setSendingMtaIp($failure->{'sending-mta-ip'});
                        if(property_exists($failure, 'receiving-ip')) {
                            $dbfailure->setReceivingIp($failure->{'receiving-ip'});
                        }
                        if($dbmxrecord) {
                            $dbfailure->setReceivingMxHostname($dbmxrecord);
                        }
                        $dbfailure->setFailedSessionCount($failure->{'failed-session-count'});
                        $this->em->persist($dbfailure);
                        $this->em->flush();
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            $log = new Logs();
            $log->setTime(new \DateTime());
            $log->setState(StateType::Fail);
            $log->setMessage("Exception while procesing MTA-STS report");
            $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $e->getMessage()))));
            $log->setMailcount(1);
            $this->em->persist($log);
            $lock->setValue('0');
            $this->em->persist($lock);
            $this->em->flush();
            return false;
        } catch (\Error $e) {
            $log = new Logs();
            $log->setTime(new \DateTime());
            $log->setState(StateType::Fail);
            $log->setMessage("Error while procesing MTA-STS report");
            $log->setDetails(array('count' => 0, 'reports' => array(array('type' => ReportType::Other, 'state' => StateType::Fail, 'message' => $e->getMessage()))));
            $log->setMailcount(1);
            $this->em->persist($log);
            $lock->setValue('0');
            $this->em->persist($lock);
            $this->em->flush();
            return false;
        }
    }
}
