<?php

namespace App\Exceptions;

// une interface est un contrat
// N'a pas de code
// contient des cont et des prototypes de méthodes 
interface HttpExceptionInterface
{
    public function getStatusCode(): int;
}
