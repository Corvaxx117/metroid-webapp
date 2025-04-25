<?php

// Charge les dépendances via l’autoloader
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use App\Core\Launcher;

// Créer une instance de la classe Launcher
$launcher = new Launcher(__DIR__ . '/../config/route.yaml');

// Démarrer l'application
$launcher->run();
