<?php

namespace Mini\Exceptions;

abstract class HttpExceptionAbstract extends \Exception implements HttpExceptionInterface
{
    protected int $statusCode;
    protected const string MESSAGE = "Une erreur est survenue.";

    public function __construct(string $message = self::MESSAGE, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
