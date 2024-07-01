<?php

namespace App\Response;

class MailboxResponse
{
    private bool $success = false;
    private string $message = 'Unknown error.';
    private array $details = array();

    public function setSuccess($success, $message = '', $details = array())
    {
        $this->success = $success;
        $this->message = $message;
        $this->details = $details;
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

    public function getDetails()
    {
        return $this->details;
    }
}
