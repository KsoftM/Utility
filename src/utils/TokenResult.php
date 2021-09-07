<?php

namespace ksoftm\utils;

class TokenResult
{
    protected bool $valid = false;
    protected null|string $msg =  null;
    /**
     * Class constructor.
     */
    public function __construct(bool $valid, ?string $msg)
    {
        $this->valid = $valid;
        $this->msg = $msg;
    }

    public function getMessage(): string
    {
        return empty($this->msg) ? "Unknown errors founded." : $this->msg;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }
}
