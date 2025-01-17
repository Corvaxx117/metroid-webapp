<?php

namespace App\Core;

use App\Core\Router;
use App\Core\ErrorHandler;
use Symfony\Component\Dotenv\Dotenv;

class Launcher
{
    private Router $router;
    private ErrorHandler $errorHandler;

    public function __construct(string $routesFile)
    {
        $this->initializeEnvironment();
        $this->errorHandler = new ErrorHandler();
        $this->router = new Router($routesFile);

        // Configurer un gestionnaire global pour les exceptions non capturées
        set_exception_handler([$this->errorHandler, 'handle']);
    }

    /**
     * Initialise les variables d'environnement
     */
    private function initializeEnvironment(): void
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../.env');
        if (file_exists(__DIR__ . '/../../.envlocal')) {
            $dotenv->load(__DIR__ . '/../../.envlocal');
        }
    }

    /**
     * Démarre l'application en fonction de la route et de la requête
     */
    public function run(): void
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];

            // Obtenir le chemin de base et la requête
            $basePath = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
            $requestUri = '/' . trim(substr($_SERVER['REQUEST_URI'], strlen($basePath)), '/');
            $uri = parse_url($requestUri, PHP_URL_PATH);

            // Résoudre la route
            $route = $this->router->match($uri, $method);

            // Instancier le contrôleur et appeler l'action
            $controllerInstance = new $route['callable']();
            call_user_func_array($controllerInstance, $route['params']);
        } catch (\Throwable $e) {
            $this->errorHandler->handle($e);
        }
    }
}
