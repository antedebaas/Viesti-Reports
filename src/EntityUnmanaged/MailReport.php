<?php

namespace App\EntityUnmanaged;

use App\Enums\ReportType;

class MailReport
{
    private bool $success = false;
    private string $message = 'Unknown error.';
    private string $mailid = '';
    private ReportType $reporttype = ReportType::Unknown;
    private object $report;

    public function setSuccess($success, $message = '')
    {
        $this->success = $success;
        $this->message = $message;
        return $this;
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMailId($mailid)
    {
        $this->mailid = $mailid;
        return $this;
    }

    public function getMailId()
    {
        return $this->mailid;
    }

    public function setReportType($reporttype)
    {
        $this->reporttype = $reporttype;
        return $this;
    }

    public function getReportType()
    {
        return $this->reporttype;
    }

    public function setReport($report)
    {
        $this->report = $report;
        return $this;
    }

    public function getReport()
    {
        return $this->report;
    }
}
