<?php

namespace App\Response;

class BaseResponse
{
    private bool $success = false;
    private string $message = 'Unknown error.';

    public function setSuccess($success, $message = '') {
        $this->success = $success;
        $this->message = $message;
        return $this;
    }

    public function getSuccess() {
        return $this->success;
    }

    public function getMessage() {
        return $this->message;
    }
}