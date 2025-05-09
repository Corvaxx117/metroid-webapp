<?php

namespace Metroid\ErrorHandler;

use Throwable;
use Metroid\View\ViewRenderer;
use Metroid\Exceptions\HttpExceptionInterface;

class ErrorHandler
{

    public function __construct(private ViewRenderer $viewRenderer) {}

    /**
     * @param HttpExceptionInterface|Throwable $exception
     */
    public function handle(Throwable $exception): void
    {
        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : 500;

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
        $fallbackPath = VIEW_PATH . 'system-errors.phtml';

        if (file_exists($fallbackPath)) {
            include $fallbackPath;
        } else {
            echo "<h1>Erreur {$statusCode}</h1>";
            echo "<p>{$message}</p>";
            echo "<p>{$description}</p>";
        }
    }
}
