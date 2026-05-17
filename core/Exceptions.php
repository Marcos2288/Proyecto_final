<?php

class ValidationException extends Exception
{
    private array $errors;

    public function __construct(array $errors, string $message = 'Validation failed', int $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

class AuthenticationException extends Exception {}

class AuthorizationException extends Exception {}

class NotFoundException extends Exception
{
    public function __construct(string $resource = 'Recurso', mixed $identifier = null, int $code = 404)
    {
        $message = $resource . ($identifier !== null ? " no encontrado: $identifier" : ' no encontrado');
        parent::__construct($message, $code);
    }
}

class DatabaseException extends Exception {}

class InsufficientStockException extends Exception
{
    private int $available;
    private int $requested;

    public function __construct(int $available, int $requested, string $message = 'Stock insuficiente', int $code = 409)
    {
        parent::__construct($message, $code);
        $this->available = $available;
        $this->requested = $requested;
    }

    public function getAvailable(): int
    {
        return $this->available;
    }

    public function getRequested(): int
    {
        return $this->requested;
    }
}
