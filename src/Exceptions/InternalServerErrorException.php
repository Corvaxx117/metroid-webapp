<?php

namespace Mini\Exceptions;

class InternalServerErrorException implements HttpExceptionAbstract
{
    protected int $statusCode = 500;
    protected const string MESSAGE = "Une erreur interne est survenue.";
}
