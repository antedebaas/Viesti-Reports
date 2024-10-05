<?php

namespace App\Response;

use App\Enums\ReportType;
use App\Enums\StateType;

class MailReportResponse
{
    public StateType $state = StateType::Fail;
    public string $message = 'Unknown error.';
    public string $mailid = '';
    public ReportType $type = ReportType::Other;
    public ?object $report;

    public function setState($state, $message = '')
    {
        $this->state = $state;
        $this->message = $message;
        return $this;
    }

    public function getState()
    {
        return $this->state;
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

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getReport()
    {
        return $this->report;
    }

    public function setReport($report)
    {
        $this->report = $report;
        return $this;
    }
}
