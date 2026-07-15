<?php

namespace App\Exceptions;

use RuntimeException;

class ContpaqSyncException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly ?int $upstreamStatus = null,
        private readonly ?string $upstreamMessage = null,
        private readonly string $errorType = 'upstream_error',
        private readonly ?string $localFolio = null,
    ) {
        parent::__construct($message);
    }

    public function upstreamStatus(): ?int
    {
        return $this->upstreamStatus;
    }

    public function upstreamMessage(): ?string
    {
        return $this->upstreamMessage;
    }

    public function errorType(): string
    {
        return $this->errorType;
    }

    public function localFolio(): ?string
    {
        return $this->localFolio;
    }
}
