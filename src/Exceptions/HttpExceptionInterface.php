<?php

namespace Metroid\Exceptions;

/**
 * Interface HttpExceptionInterface
 *
 * @package Metroid\Exceptions
 * @method int getStatusCode()
 */
interface HttpExceptionInterface
{
    public function getStatusCode(): int;
}
