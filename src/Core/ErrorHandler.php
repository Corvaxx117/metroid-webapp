<?php

namespace App\Core;

use Throwable;
use App\Services\ViewRenderer;

class ErrorHandler
{
    private const EXCEPTION_TO_STATUS = [
        'App\Exceptions\NotFoundException' => 404,
        'App\Exceptions\ForbiddenException' => 403,
        'App\Exceptions\InternalServerErrorException' => 500,
        'App\Exceptions\UnauthorizedException' => 401,
        'App\Exceptions\BadRequestException' => 400,
        'App\Exceptions\ConflictException' => 409,
        'App\Exceptions\ServiceUnavailableException' => 503,
        'App\Exceptions\UnsupportedMediaTypeException' => 415,
    ];



    public function __construct(private ViewRenderer $viewRenderer) {}

    public function handle(Throwable $exception): void
    {
        $statusCode = $this->getStatusCodeForException($exception);
        $message = $exception->getMessage() ?: "Une erreur inattendue est survenue.";
        try {
            $this->viewRenderer->render('system-errors.phtml', [
                'statusCode' => $statusCode,
                'message' => $message,
                'description' => $exception->getTraceAsString(),
            ], $statusCode);
        } catch (\Throwable $e) {
            $this->renderFallbackError($statusCode, $message);
        }
    }

    /**
     * Retourne le code HTTP pour une exception donnée.
     */
    private function getStatusCodeForException(Throwable $exception): int
    {
        foreach (self::EXCEPTION_TO_STATUS as $class => $code) {
            if ($exception instanceof $class) {
                return $code;
            }
        }

        return $exception->getCode() ?: 500; // Code par défaut
    }
    private function renderFallbackError(int $statusCode, string $message): void
    {
        http_response_code($statusCode);

        // Rendre les variables disponibles dans la vue
        $description = "Une erreur critique est survenue.";
        include __DIR__ . "/../../views/system-errors.phtml";
    }
}
