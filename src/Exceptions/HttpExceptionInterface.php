<?php

namespace App\Exceptions;

/**
 * Interface HttpExceptionInterface
 * 
 * @package App\Exceptions
 * @method int getStatusCode()
 */
interface HttpExceptionInterface
{
    public function getStatusCode(): int;
}
