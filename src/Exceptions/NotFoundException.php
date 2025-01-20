<?php

namespace App\Exceptions;

class NotFoundException extends \Exception implements HttpExceptionInterface
{
    protected int $statusCode = 404;
    protected $message = "La ressource demandée est introuvable.";

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
