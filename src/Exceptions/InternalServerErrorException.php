<?php

namespace App\Exceptions;

class InternalServerErrorException extends \Exception
{
    protected $code = 500;
    protected $message = "Une erreur interne est survenue.";
}
