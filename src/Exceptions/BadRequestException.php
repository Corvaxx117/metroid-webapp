<?php

namespace Metroid\Exceptions;

class BadRequestException extends HttpExceptionInterface
{
    protected int $statusCode = 400;
    protected const string MESSAGE = "Requête invalide.";
}
