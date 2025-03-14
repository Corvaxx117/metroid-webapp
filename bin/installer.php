<?php

echo "Bienvenue dans l'installation de votre projet 🎉\n";

$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    copy(__DIR__ . '/../.env.example', $envFile);
    echo "✅ Fichier .env créé.\n";
}

echo "📦 Installation des dépendances...\n";
exec('composer install');

echo "📡 Configuration de la base de données...\n";
exec('php bin/setup-database.php');

echo "✅ Installation terminée ! 🎉\n";

echo "🚀 Lancement de l'application...\n";
exec('php bin/server.php');
