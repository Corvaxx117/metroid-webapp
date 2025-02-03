<?php

namespace App\Exceptions;

use App\Exceptions\HttpExceptionInterface;

class InternalServerErrorException extends \Exception implements HttpExceptionInterface
{
    private int $statusCode = 500;
    private string $message = "Une erreur interne est survenue.";

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
