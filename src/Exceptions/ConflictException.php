<?php

namespace Mini\Exceptions;

class ConflictException extends HttpExceptionAbstract
{
    protected int $code = 409;
    protected const string MESSAGE = "Conflit avec une ressource existante.";
}
