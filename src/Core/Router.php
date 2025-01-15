<?php

// Rôle : Analyse les requêtes (URI et méthode HTTP) et les fait correspondre aux routes définies.
// Fonctionnement :
// Charge les routes depuis routes.yaml.
// Utilise des expressions régulières pour gérer les routes dynamiques (ex. : /news/:id).
// Retourne le contrôleur et les paramètres associés à une route.

namespace App\Core;

use Symfony\Component\Yaml\Yaml;

class Router
{
    private array $routes;

    public function __construct(string $routesFile)
    {
        $this->routes = Yaml::parseFile($routesFile)['routes'];
    }

    public function match(string $uri, string $method)
    {
        foreach ($this->routes as $route => $config) {
            $pattern = preg_replace('/:\w+/', '(\w+)', str_replace('/', '\/', $route));
            // $matches contient tous les paramètres extrait de l'URI
            // arguments de pregmatch optionnel passé par référence (il est alimenté directement par cette fonction )
            // Voir parentheses capturantes

            // le preg_match permet de tester si l'url de la requête correspond bien à la route
            // il alimente $matches avec toutes les valeurs variable de la route par rapport à l'url
            if (preg_match('/^' . $pattern . '$/', $uri, $matches) && $method === $config['method']) {
                // Supprime le 1erelement du tableau matches
                // $matches[0] contiendra toujours ce que l'expression reguliere valide dans sa totalité, 
                // la ou les index suivant contiendront seulement ce qui est capturé 
                // (par capture j'entends les parenthèse de la regexp)
                array_shift($matches); // Remove the full match
                // si une route est matchée on retourne donc un array structuré qui va nous être utile pour appeler le bon controller avec les bons arguments
                return ['callable' => $config['callable'], 'params' => $matches];
            }
        }
        throw new \Exception("Aucune route concordante pour l'URI: $uri");
    }
}
