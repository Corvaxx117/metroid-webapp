<?php

namespace App\Exceptions;

class ServiceUnavailableException extends \Exception
{
    protected $code = 503;
    protected $message = "Service temporairement indisponible.";
}
