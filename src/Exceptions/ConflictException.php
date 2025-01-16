<?php

namespace App\Exceptions;

class ConflictException extends \Exception
{
    protected $code = 409;
    protected $message = "Conflit avec une ressource existante.";
}
