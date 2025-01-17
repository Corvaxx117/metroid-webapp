<?php

namespace App\Controller;

use App\Services\ViewRenderer;

class BaseController
{
    public function __construct(protected ViewRenderer $viewRenderer) {}

    protected function render(string $view, array $data = [], int $statusCode = 200): void
    {
        $this->viewRenderer->render($view, $data, $statusCode);
    }

    protected function renderError(string $message, int $statusCode = 500, string $description = ''): void
    {
        $this->viewRenderer->render('system-errors.phtml', [
            'statusCode' => $statusCode,
            'message' => $message,
            'description' => $description,
        ], $statusCode);
    }
}
