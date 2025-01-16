<?php

// (Point d'entrée de l'application)
// Rôle : Toutes les requêtes passent par ce fichier.
// Exécute l'action appropriée du contrôleur.

// Creer une class laucher qui doit loader les routes 
// Ce fichier pourrait etre reutilisé par la suite sur d'autre projets pour avoir une base de code commun
// a la racine de src 
// composer.json peut lister des dépots privés 

// Charge les dépendances via l’autoloader.
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\ErrorHandler;
use App\Core\Router;
use Symfony\Component\Dotenv\Dotenv;

// Charge les variables d'environnement
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');
if (file_exists(__DIR__ . '/../.envlocal')) {
    $dotenv->load(__DIR__ . '/../.envlocal');
}

// Initialiser le gestionnaire d'erreurs
$errorHandler = new ErrorHandler();

// Configurer un gestionnaire d'erreur global
// Permet de regrouper toutes les erreurs non capturées en un seul endroit
set_exception_handler([$errorHandler, 'handle']);

try {
    // Initialise le système de routage.
    $router = new Router(__DIR__ . '/../config/route.yaml');
    // Récupérer la requête
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
    // Résolution de la route
    $route = $router->match($uri, $method);

    // Appeler le contrôleur et l'action
    $controllerInstance = new $route['callable']();
    call_user_func_array($controllerInstance, $route['params']);
} catch (\Throwable $e) {
    throw $e;
}
