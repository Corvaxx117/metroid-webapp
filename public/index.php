<?php

// Charge les dépendances via l’autoloader
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
// Si pas de valeur dans le .env, on va la cherche dans $_SERVER[cle ?]
// creer un service avec une methode de classe pour generer automatiquement les routes (return le base path)
// Service qui prend un tableau assoc avec les parametres de GET 
// UrlGenerator::getUrlFromPath('/user/:id', ['id' => 2, 'foo' => 'bar'])
// https://baseUrl/user/2?foo=bar
// UrlGenerator::getUrlFromPath('/user/2', ['foo' => 'bar'])

use App\Core\Launcher;

// Créer une instance de la classe Launcher
$launcher = new Launcher(__DIR__ . '/../config/route.yaml');

// Démarrer l'application
$launcher->run();
