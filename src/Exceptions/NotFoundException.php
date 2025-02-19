<?php

namespace App\Exceptions;

class NotFoundException extends HttpExceptionAbstract
{
    protected int $statusCode = 404;
    protected const string MESSAGE = "La ressource demandée est introuvable.";
}
