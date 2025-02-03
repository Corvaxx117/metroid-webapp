<?php

namespace App\Exceptions;

use App\Exceptions\HttpExceptionInterface;

class UnsupportedMediaTypeException extends \Exception implements HttpExceptionInterface
{
    private int $statusCode = 415;
    private string $message = "Type de contenu non pris en charge.";

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
