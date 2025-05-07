<?php

echo "🎉 Installation de votre projet basé sur Corvaxx Starter WebApp 🎉\n";

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
            echo "📁 Dossier créé : " . $subPath . "\n";
        }
    } else {
        if (!file_exists($targetPath)) {
            copy($item, $targetPath);
            echo "✅ Fichier copié : " . $subPath . "\n";
        } else {
            echo "ℹ️  Fichier déjà existant : " . $subPath . "\n";
        }
    }
}


// Générer le composer.json du projet utilisateur
$projectComposerJson = [
    "name" => "app/my-project",
    "description" => "Projet basé sur corvaxx/starter-webapp",
    "type" => "project",
    "require" => [
        "php" => ">=8.0",
        "ext-pdo" => "*",
        "ext-mbstring" => "*",
        "symfony/dotenv" => "^6.0",
        "corvaxx/starter-webapp" => "dev-main"
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
echo "✅ Fichier composer.json généré\n";

// Lancer composer update pour installer le framework dans /vendor
echo "🔄 Installation des dépendances avec Composer...\n";
chdir($baseDir);
exec('composer update');

echo "✅ Projet Metroid initialisé ! 🎉\n";
echo "🚀 Lancer le serveur avec : php -S localhost:8000 -t public\n";
