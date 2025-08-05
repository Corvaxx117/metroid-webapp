<?php

namespace Metroid\ErrorHandler;

use Throwable;
use Metroid\View\ViewRenderer;
use Metroid\Exceptions\HttpExceptionInterface;

class ErrorHandler
{

    public function __construct(private ViewRenderer $viewRenderer) {}


    /**
     * Enregistre l'exception dans le journal d'erreurs et la rend visible
     * dans une page d'erreur.
     *
     * @param Throwable $exception L'exception levée
     *
     * @return void
     */
    public function handle(Throwable $exception): void
    {
        // Enregistrer l'exception dans le journal
        error_log(sprintf(
            "[%s] %s in %s:%d\nStack trace:\n%s",
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        ));

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

    /**
     * Gère une erreur critique en rendant une page d'erreur simplifiée,
     * même si le système de vues est défectueux.
     *
     * @param int    $statusCode Code d'état HTTP
     * @param string $message    Message de l'exception
     */
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
