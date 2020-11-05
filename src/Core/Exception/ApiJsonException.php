<?php

namespace App\Core\Exception;

class ApiJsonException extends \JsonException
{
    private ?int $statusCode = null;

    private array $errors = [];

    public function __construct(int $statusCode, string $message = null, array $errors = [], \Throwable $previous = null)
    {
        $this->statusCode = $statusCode;

        $this->errors = $errors;

        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}