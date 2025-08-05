<?php

// Ce fichier contient la classe Router.
// Son rôle est de faire correspondre une URL et une méthode HTTP (GET, POST, etc.)
// avec une route définie dans un fichier YAML, et de retourner le contrôleur à appeler.

namespace Metroid\Router;

use Symfony\Component\Yaml\Yaml;
use Metroid\Exceptions\NotFoundException;

class Router
{
    // Contiendra toutes les routes déclarées (provenant du fichier routes.yaml)
    private array $routes;

    /**
     * Le constructeur charge les routes depuis le fichier YAML
     * @param string $routesFile chemin du fichier routes.yaml
     */
    public function __construct(string $routesFile)
    {
        // Charge les routes dans le tableau $this->routes
        $this->routes = Yaml::parseFile($routesFile)['routes'];
    }

    /**
     * Tente de faire correspondre une URI et une méthode HTTP à une route.
     *
     * @param string $uri L'URL demandée (ex: /books/42)
     * @param string $method La méthode HTTP (GET, POST, etc.)
     * @return array Un tableau contenant le contrôleur, la méthode et les paramètres associés
     * @throws NotFoundException Si aucune route ne correspond
     */
    public function match(string $uri, string $method): array
    {
        // Nettoyage du chemin : on retire le dossier "public" ou autre préfixe
        // Exemple : /mon_dossier/nom_du_projet/public devient /
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']); // ex: /mon_dossier/nom_du_projet/public
        if (str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }

        // On force l'URI à toujours commencer par un /
        $uri = '/' . ltrim($uri, '/');

        // On parcourt toutes les routes définies dans le YAML
        foreach ($this->routes as $route => $config) {

            // On transforme la route dynamique en expression régulière
            // Exemple : /books/:id devient \/books\/(\w+)
            $pattern = preg_replace('/:\w+/', '(\w+)', str_replace('/', '\/', $route));

            // On récupère les méthodes autorisées pour cette route (ex: GET|POST → [GET, POST])
            $allowedMethods = explode('|', $config['method']);

            // On teste si l'URI correspond à cette route ET que la méthode est autorisée
            if (preg_match('/^' . $pattern . '$/', $uri, $matches) && in_array($method, $allowedMethods)) {

                // On extrait les noms des paramètres (ex: :id, :threadId)
                preg_match_all('/:(\w+)/', $route, $paramNames);
                $paramNames = $paramNames[1]; // ex: ['id']

                // On retire la première valeur de $matches (qui contient la correspondance complète)
                array_shift($matches);

                // On associe chaque nom de paramètre à sa valeur capturée
                // Exemple : ['id' => 42]
                $params = array_combine($paramNames, $matches);

                // On récupère la cible de la route (ex: App\Controller\BookController::show)
                [$controllerClass, $controllerMethod] = explode('::', $config['callable']);

                // On retourne toutes les informations nécessaires pour appeler le contrôleur
                return [
                    'controllerClass' => $controllerClass,
                    'controllerMethod' => $controllerMethod,
                    'params' => $params
                ];
            }
        }

        // Si aucune route ne correspond, on lance une exception personnalisée
        throw new NotFoundException("Aucune route ne correspond à $uri ($method)");
    }
}
