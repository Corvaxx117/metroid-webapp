<?php

namespace Metroid;

use Metroid\Container\ServiceContainer;
use Metroid\ErrorHandler\ErrorHandler;
use Metroid\View\ViewRenderer;
use Metroid\FlashMessage\FlashMessage;
use Metroid\Router\Router;
use Metroid\Http\Request;
use Metroid\Http\Response;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Classe Launcher
 * Classe responsable de lancement de l'application
 * @package Metroid\Core
 */
class Launcher
{
    private ServiceContainer $container;
    private Router $router;
    private ErrorHandler $errorHandler;
    private string $basePath;

    public function __construct(string $basePath, string $routesFile)
    {
        // Initialise le chemin de base
        $this->basePath = rtrim($basePath, '/') . '/';
        $this->initializeEnvironment();
        // Création du conteneur
        $this->container = new ServiceContainer();
        // Enregistre ErrorHandler dans le conteneur
        $this->container->set(ErrorHandler::class, fn($c) => new ErrorHandler(
            $c->get(ViewRenderer::class)
        ));

        $this->errorHandler = $this->container->get(ErrorHandler::class);

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
        $envFile = $this->basePath . '.env';

        if (file_exists($envFile)) {
            $dotenv->load($envFile);
        }

        $envLocal = $this->basePath . '.env.local';
        if (file_exists($envLocal)) {
            $dotenv->load($envLocal);
        }
    }

    /**
     * Démarre l'application en fonction de la route et de la requête
     */
    public function run(): void
    {
        session_start();

        try {
            $method = $_SERVER['REQUEST_METHOD'];

            // Astuce php qui permet de traiter la partie de l'url qui nous interresse
            // On detecte le fichier index.php et on en extrait le chemin
            // Si l'URL est http://localhost/public/index.php/news, $basePath devient /public/
            $basePath = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
            // dans $basePath on va avoir le chemin vers le fichier index.php
            // On utilise ce basePath pour le retirer
            // Il ne restera que ce qui suit le public/ Exemple : /news.
            $requestUri = '/' . trim(substr($_SERVER['REQUEST_URI'], strlen($basePath)), '/');
            // Extrait le chemin sans les paramètres Exemple : /news si l'URL est /news?id=123
            $uri = parse_url($requestUri, PHP_URL_PATH);

            // Création de l'objet Request
            $request = $this->container->get(Request::class);

            // Résoudre la route
            $route = $this->router->match($uri, $method);
            $controllerClass = $route['controllerClass'];
            $method = $route['methodName'];
            $params = [$request, ...$route['params']];
            $controller = new $controllerClass(
                $this->container->get(ViewRenderer::class),
                $this->container->get(FlashMessage::class)
            );
            // Exécuter le contrôleur
            $response = call_user_func_array([$controller, $method], $params);

            // Gérer la réponse
            if ($response instanceof Response) {
                $response->send();
            } elseif (is_string($response)) {
                echo $response;
            } else {
                throw new \RuntimeException("Le contrôleur n'a pas retourné de réponse valide.");
            }
        } catch (\Throwable $e) {
            $this->errorHandler->handle($e);
        }
    }
}
