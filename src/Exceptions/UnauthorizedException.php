<?php

namespace Metroid\Exceptions;

class UnauthorizedException extends HttpExceptionAbstract
{
    protected int $code = 401;
    protected const string MESSAGE = "Authentification requise.";
}
