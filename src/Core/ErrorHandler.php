<?php

namespace App\Core;

use Throwable;
use App\Services\ViewRenderer;
use App\Exceptions\HttpExceptionInterface;

class ErrorHandler
{
    private ViewRenderer $viewRenderer;

    public function __construct()
    {
        $this->viewRenderer = new ViewRenderer();
    }

    public function handle(Throwable $exception): void
    {
        // Si l'exception est de type HttpExceptionInterface
        // Alors on recupere le code HTTP
        // sinon on renvoie 500
        // if ($exception instanceof HttpExceptionInterface) {
        //     $statusCode = $exception->getStatusCode();
        // } else {
        //     $statusCode = 500;
        // }
        // ?? retourne le 1er membre non null

        // Voir les httpExceptions de Symfony pour rendre ça plus generique 
        // Revoir les interfaces et les traits 
        // comment instancier PDO et le faire transiter dans le projet 
        // -> Voir pattern singleton
        // Apres ça si  j'ai le temps integrer le projet ocr
        // Faire une copie sur un depot git différent

        $statusCode = $exception?->getStatusCode() ?? 500;
        $message = $exception->getMessage() ?: "Une erreur inattendue est survenue.";
        try {
            $this->viewRenderer->render('system-errors.phtml', [
                'statusCode' => $statusCode,
                'message' => $message,
                // 'description' => $exception->getTraceAsString(),
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
        include __DIR__ . "/../../views/system-errors.phtml";
    }
}
