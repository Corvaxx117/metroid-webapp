<?php

namespace Mini\ErrorHandler;

use Throwable;
use Mini\View\ViewRenderer;
use Mini\Exceptions\HttpExceptionInterface;

class ErrorHandler
{
    private ViewRenderer $viewRenderer;

    public function __construct()
    {
        $this->viewRenderer = new ViewRenderer();
    }

    /**
     * @param HttpExceptionInterface|Throwable $exception
     */
    public function handle(Throwable $exception): void
    {
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }
        $message = $exception->getMessage() ?: "Une erreur inattendue est survenue.";
        try {
            $this->viewRenderer->render('system-errors.phtml', [
                'statusCode' => $statusCode,
                'message' => $message,
                'trace' => $exception->getTrace(),
            ], $statusCode);
        } catch (\Throwable $e) {
            $this->renderFallbackError($statusCode, $message);
        }
    }

    private function renderFallbackError(int $statusCode, string $message): void
    {
        http_response_code($statusCode);

        // Rendre les variables disponibles dans la vue
        $description = "Une erreur critique est survenue.";
        $trace = [];
        include __DIR__ . "/system-errors.phtml";
    }
}
