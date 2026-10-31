<?php

declare(strict_types=1);

namespace LiveApi;

use RuntimeException;

final class LiveApiException extends RuntimeException
{
    public function __construct(
        public readonly int $status,
        public readonly string $errorCode,
        string $message,
    ) {
        parent::__construct($message, $status);
    }
}
