<?php

// (Point d'entrée de l'application)

// Rôle : Toutes les requêtes passent par ce fichier.
// Fonctionnement :
// Il charge les dépendances via l’autoloader.
// Charge les variables d'environnement depuis les fichiers .env et .envlocal.
// Initialise le système de routage.
// Correspond chaque requête (URI et méthode HTTP) à une route définie dans le fichier routes.yaml.
// Exécute l'action appropriée du contrôleur.

// L'index doit  require l'autoloader et charger les variables d'environnement 
// Creer une class laucher qui doit loader les routes 
// Ce fichier pourrait etre reutiliser par la suite  sur d'autre projets pour avoir une base de code commun
// a la racine de src 
// composer.json peut lister des dépots privés 

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use Symfony\Component\Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');
if (file_exists(__DIR__ . '/../.envlocal')) {
    $dotenv->load(__DIR__ . '/../.envlocal');
}

// Charger les routes
$router = new Router(__DIR__ . '/../config/route.yaml');

// Récupérer la requête
$method = $_SERVER['REQUEST_METHOD'];


// Astuce php qui permet de traiter la partiede l'url qui nousinterresse 
// On detecte le fichierindex.php et on en extrait le chemin
$basePath = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
// var_dump($_SERVER['SCRIPT_NAME']);
//here we handle specific environment path. For example, the uri is not the same locally with no subdomain then in production were domain without path leads to the public/index.php
// dans $basePath on va avoir le chemin vers le fichier index.php
// On utilise ce basePath pour le retirer 
// Il ne restera que ce qui suit le public/
$requestUri = '/' . trim(substr($_SERVER['REQUEST_URI'], strlen($basePath)), '/');
$uri = parse_url($requestUri, PHP_URL_PATH);

// var_dump($requestUri);
try {
    $route = $router->match($uri, $method);
    // [$controller, $action] = explode('::', $route['callable']);

    $params = $route['params'];

    // Appeler le contrôleur et l'action
    $controllerInstance = new $route['callable']();
    // 
    call_user_func_array($controllerInstance, $params);
} catch (Exception $e) {
    http_response_code(404);
    echo "Error: " . $e->getMessage();
}
