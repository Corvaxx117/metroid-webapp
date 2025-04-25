<?php

namespace Mini\Exceptions;

/**
 * Interface HttpExceptionInterface
 *
 * @package Mini\Exceptions
 * @method int getStatusCode()
 */
interface HttpExceptionInterface
{
    public function getStatusCode(): int;
}
