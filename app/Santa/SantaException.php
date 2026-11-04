<?php

namespace App\Santa;

use RuntimeException;

class SantaException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $status = 502,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
