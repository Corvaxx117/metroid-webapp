<?php

namespace Metroid\Exceptions;

class UnsupportedMediaTypeException extends HttpExceptionAbstract
{
    protected int $statusCode = 415;
    protected const string MESSAGE = "Type de contenu non pris en charge.";
}
