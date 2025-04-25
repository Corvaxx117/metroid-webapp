<?php

namespace App\Exceptions;

class ForbiddenException extends HttpExceptionAbstract
{
    protected int $code = 403;
    protected const string MESSAGE = "Accès interdit.";
}
