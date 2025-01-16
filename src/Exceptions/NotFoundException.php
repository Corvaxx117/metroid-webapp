<?php

namespace App\Exceptions;

class NotFoundException extends \Exception
{
    protected $code = 404;
    protected $message = "La ressource demandée est introuvable.";
}
