<?php

namespace App\Exceptions;

class UnauthorizedException extends \Exception
{
    protected $code = 401;
    protected $message = "Authentification requise.";
}
