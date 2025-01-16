<?php

namespace App\Exceptions;

class ForbiddenException extends \Exception
{
    protected $code = 403;
    protected $message = "Accès interdit.";
}
