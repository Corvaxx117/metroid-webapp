<?php

namespace App\Exceptions;

class ServiceUnavailableException extends HttpExceptionAbstract
{
    protected int $code = 503;
    protected const string MESSAGE = "Service temporairement indisponible.";
}
