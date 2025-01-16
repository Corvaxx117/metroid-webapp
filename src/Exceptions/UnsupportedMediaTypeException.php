<?php

namespace App\Exceptions;

class UnsupportedMediaTypeException extends \Exception
{
    protected $code = 415;
    protected $message = "Type de contenu non pris en charge.";
}
