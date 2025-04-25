<?php

echo "🎉 Bienvenue dans l'installation du projet MVC-Starter 🎉\n";

$baseDir = dirname(__DIR__);

// Dossiers à créer
$directories = [
    'var',
    'var/cache',
    'var/log'
];

foreach ($directories as $dir) {
    $path = $baseDir . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        echo "📁 Dossier créé : $dir\n";
    } else {
        echo "ℹ️  Dossier déjà existant : $dir\n";
    }
}

// Copie du .env si nécessaire
$envFile = $baseDir . '/.env';
if (!file_exists($envFile)) {
    copy($baseDir . '/.env.example', $envFile);
    echo "✅ Fichier .env créé.\n";
} else {
    echo "ℹ️  Fichier .env déjà présent. Aucun changement.\n";
}

echo "✅ Installation terminée ! 🎉\n";
echo "🚀 Pour lancer le serveur local : php bin/server.php\n";
