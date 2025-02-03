<?php

namespace App\Exceptions;

class NotFoundException extends \Exception implements HttpExceptionInterface
{
    protected int $statusCode = 404;
    protected const MESSAGE = "La ressource demandée est introuvable.";

    public function __construct(string $message = self::MESSAGE, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
