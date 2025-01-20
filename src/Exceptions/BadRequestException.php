<?php

namespace App\Exceptions;

class BadRequestException extends \Exception implements HttpExceptionInterface
{
    protected int $statusCode = 400;
    protected string $message = "Requête invalide.";

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
