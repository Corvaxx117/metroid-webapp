<?php

namespace App\Core;

use Throwable;

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

    public function handle(Throwable $exception): void
    {
        $statusCode = $this->getStatusCodeForException($exception);
        $message = $exception->getMessage() ?: "Une erreur inattendue est survenue.";

        $this->renderErrorView($statusCode, $message);
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

    /**
     * Rend la vue d'erreur.
     */
    protected function renderErrorView(int $statusCode, string $message): void
    {
        http_response_code($statusCode);

        // Définir la base URL dynamiquement
        $baseUrl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        // Variables à transmettre à la vue
        $data = [
            'statusCode' => $statusCode,
            'message' => $message,
            'baseUrl' => $baseUrl,
        ];

        // Inclut la vue avec les variables
        $this->loadView('system-errors.phtml', $data);
    }

    protected function loadView(string $view, array $data): void
    {
        extract($data); // Rend les clés du tableau accessibles comme variables
        require_once __DIR__ . "/../../views/{$view}";
    }
}
