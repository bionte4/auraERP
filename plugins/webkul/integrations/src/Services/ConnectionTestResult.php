<?php

namespace Webkul\Integrations\Services;

class ConnectionTestResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
    ) {}

    public static function ok(string $message): self
    {
        return new self(true, $message);
    }

    public static function fail(string $message): self
    {
        return new self(false, $message);
    }

    public function status(): string
    {
        return $this->success ? 'success' : 'failed';
    }
}
