<?php

// Rôle : Analyse les requêtes (URI et méthode HTTP) et les fait correspondre aux routes définies.
// Fonctionnement :
// Charge les routes depuis routes.yaml.
// Utilise des expressions régulières pour gérer les routes dynamiques (ex. : /news/:id).
// Retourne le contrôleur et les paramètres associés à une route.

namespace Metroid\Router;

use Symfony\Component\Yaml\Yaml;
use Metroid\Exceptions\NotFoundException;
use Metroid\Exceptions\InternalServerErrorException;

class Router
{
    private array $routes;

    public function __construct(string $routesFile)
    {
        // Injection des dépendances nécessaires au contrôleur
        $this->routes = Yaml::parseFile($routesFile)['routes'];
        // // Configurer un gestionnaire global pour les exceptions non capturées
        // set_exception_handler([$this->errorHandler, 'handle']);
    }

    public function match(string $uri, string $method)
    {
        foreach ($this->routes as $route => $config) {
            // Convertir les paramètres dynamiques (ex : :id) en regexp capturante
            $pattern = preg_replace('/:\w+/', '(\w+)', str_replace('/', '\/', $route));

            $allowedMethods = explode('|', $config['method']);
            if (preg_match('/^' . $pattern . '$/', $uri, $matches) && in_array($method, $allowedMethods)) {
                array_shift($matches); // Supprimer la correspondance complète

                // Récupération du callable (ex: App\Controller\HomeController::index)
                $classDefinition = explode('::', $config['callable']);

                if (count($classDefinition) !== 2) {
                    throw new InternalServerErrorException("Le callable '{$config['callable']}' est invalide.");
                }

                return [
                    'controllerClass' => $classDefinition[0],
                    'methodName' => $classDefinition[1],
                    'params' => $matches
                ];
            }
        }
        // Aucune route correspondante
        throw new NotFoundException("Aucune route correspondante pour l'URI : $uri");
    }
}
