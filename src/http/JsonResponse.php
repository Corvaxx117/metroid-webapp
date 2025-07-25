<?php

namespace Metroid\Http;

class JsonResponse extends Response
{
    public function __construct(array $data = [], int $statusCode = 200)
    {
        parent::__construct();
        $this->setContent(json_encode($data));
        $this->setStatusCode($statusCode);
        $this->setHeaders(['Content-Type' => 'application/json']);
    }
}
