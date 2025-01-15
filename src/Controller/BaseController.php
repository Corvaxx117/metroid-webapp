<?php

namespace App\Controller;

class BaseController
{
    protected function render(string $view, array $data = [], int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: text/html');
        // Extraction des variables
        extract($data);

        // Inclure le fichier de vue
        require_once __DIR__ . "../../../views/{$view}";
    }
}
