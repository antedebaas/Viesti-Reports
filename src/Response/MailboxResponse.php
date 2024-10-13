<?php

namespace App\Response;
use App\Enums\StateType;

class MailboxResponse
{
    private StateType $state = StateType::Fail;
    private string $message = 'Unknown error.';
    private array $details = array();

    public function setState($state, $message = '', $details = array())
    {
        $this->state = $state;
        $this->message = $message;
        $this->details = $details;
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

    public function getDetails()
    {
        return $this->details;
    }
}
