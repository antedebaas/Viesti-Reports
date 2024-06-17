<?php

namespace App\Response;

use App\Enums\ReportType;

class MailReportResponse
{
    public bool $success = false;
    public string $message = 'Unknown error.';
    public string $mailid = '';
    public ReportType $reporttype = ReportType::Unknown;

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
}
