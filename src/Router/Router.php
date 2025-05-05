<?php

// Rôle : Analyse les requêtes (URI et méthode HTTP) et les fait correspondre aux routes définies.
// Fonctionnement :
// Charge les routes depuis routes.yaml.
// Utilise des expressions régulières pour gérer les routes dynamiques (ex. : /news/:id).
// Retourne le contrôleur et les paramètres associés à une route.

namespace Mini\Router;

use Mini\View\ViewRenderer;
use Mini\ErrorHandler\ErrorHandler;
use Symfony\Component\Yaml\Yaml;
use Mini\Exceptions\NotFoundException;
use Mini\Exceptions\InternalServerErrorException;
use Mini\FlashMessage\FlashMessage;

class Router
{
    private array $routes;
    private ErrorHandler $errorHandler;
    private ViewRenderer $viewRenderer;
    private FlashMessage $flashMessage;

    public function __construct(string $routesFile)
    {
        // Injection des dépendances nécessaires au contrôleur
        $this->viewRenderer = new ViewRenderer();
        $this->flashMessage = new FlashMessage();
        $this->routes = Yaml::parseFile($routesFile)['routes'];
        $this->errorHandler = new ErrorHandler();
        // Configurer un gestionnaire global pour les exceptions non capturées
        set_exception_handler([$this->errorHandler, 'handle']);
    }

    public function match(string $uri, string $method)
    {
        foreach ($this->routes as $route => $config) {
            // Convertir les paramètres dynamiques (ex : :id) en regexp capturante
            $pattern = preg_replace('/:\w+/', '(\w+)', str_replace('/', '\/', $route));

            if (preg_match('/^' . $pattern . '$/', $uri, $matches) && $method === $config['method']) {
                array_shift($matches); // Supprimer la correspondance complète

                // Récupération du callable (ex: App\Controller\HomeController::index)
                $classDefinition = explode('::', $config['callable']);

                if (count($classDefinition) !== 2) {
                    throw new InternalServerErrorException("Le callable '{$config['callable']}' est invalide.");
                }
                // Récupération du nom de la classe et de la fonction
                $controllerClass = $classDefinition[0];
                $methodName = $classDefinition[1];

                // Valider que la classe et la fonction existent
                if (!class_exists($controllerClass)) {
                    throw new InternalServerErrorException("Classe contrôleur '$controllerClass' introuvable.");
                }

                if (!method_exists($controllerClass, $methodName)) {
                    throw new InternalServerErrorException("Méthode '$methodName' introuvable dans '$controllerClass'.");
                }
                // Instanciation du contrôleur
                $controller = new $controllerClass($this->viewRenderer, $this->flashMessage);

                return [
                    'callable' => [$controller, $methodName],
                    'params' => $matches
                ];
            }
        }
        // Aucune route correspondante
        throw new NotFoundException("Aucune route correspondante pour l'URI : $uri");
    }
}
