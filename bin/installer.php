<?php

function output(string $text, string $colorCode = ''): void
{
    echo $colorCode . $text . "\033[0m\n";
    flush(); // Pour forcer l'affichage dans certains contextes
}

// Titre stylisé
output("  __  __ _       _        ", "\033[1;36m");
output(" |  \\/  (_) __ _| | _____ ", "\033[1;36m");
output(" | |\\/| | |/ _` | |/ / _ \\", "\033[1;36m");
output(" | |  | | | (_| |   <  __/", "\033[1;36m");
output(" |_|  |_|_|\\__,_|_|\\_\\___|", "\033[1;36m");
output("           M   I   N   I  ", "\033[1;35m");

output("🎉 Bienvenue dans le Starter Mini Framework ! 🎉", "\033[1;32m");

$baseDir = dirname(__DIR__);
$distDir = __DIR__ . '/dist';

// Copier les fichiers de dist/ vers la racine du projet
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($distDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $item) {
    $subPath = $iterator->getInnerIterator()->getSubPathName();
    $targetPath = $baseDir . '/' . $subPath;

    if ($item->isDir()) {
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
            output("📁 Dossier créé : $subPath", "\033[1;34m");
        }
    } else {
        if (!file_exists($targetPath)) {
            copy($item, $targetPath);
            output("✅ Fichier copié : $subPath", "\033[1;32m");
        } else {
            output("ℹ️  Fichier déjà existant : $subPath", "\033[0;33m");
        }
    }
}

// Composer JSON du projet utilisateur
$projectComposerJson = [
    "name" => "app/my-project",
    "description" => "Projet basé sur corvaxx/metroid-webapp",
    "type" => "project",
    "require" => [
        "php" => ">=8.0",
        "ext-pdo" => "*",
        "ext-mbstring" => "*",
        "symfony/dotenv" => "^6.0",
        "corvaxx/metroid-webapp" => "dev-main"
    ],
    "autoload" => [
        "psr-4" => [
            "App\\" => "src/"
        ]
    ],
    "minimum-stability" => "dev",
    "prefer-stable" => true
];

file_put_contents($baseDir . '/composer.json', json_encode($projectComposerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
output("✅ Fichier composer.json généré", "\033[1;32m");

output("🔄 Installation des dépendances avec Composer...\n", "\033[1;36m");
chdir($baseDir);
exec('composer update');

output("✅ Projet initialisé !", "\033[1;32m");
output("🚀 Lancez votre serveur avec : php -S localhost:8000 -t public", "\033[1;33m");
