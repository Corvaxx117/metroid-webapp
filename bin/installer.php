<?php

echo "🎉 Bienvenue dans l'installation du projet MVC-Starter 🎉\n";

$baseDir = dirname(__DIR__);

// Dossiers à créer
$directories = [
    'var',
    'var/cache',
    'var/log',
    'config',
    'public',
    'src',
    'src/Controller',
    'src/Model',
    'views',
    'views/flashMessages'
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

// Copie des fichiers de configuration
$filesToCopy = [
    'bin/dist/.env' => '.env',
    'bin/dist/config.php' => 'config/config.php',
    'bin/dist/route.yaml' => 'config/route.yaml',
    'bin/dist/index.php' => 'public/index.php'
];

foreach ($filesToCopy as $source => $destination) {
    $sourcePath = $baseDir . '/' . $source;
    $destPath = $baseDir . '/' . $destination;

    if (file_exists($sourcePath)) {
        if (!file_exists($destPath)) {
            copy($sourcePath, $destPath);
            echo "✅ Fichier copié : $destination\n";
        } else {
            echo "ℹ️  Fichier déjà existant : $destination\n";
        }
    } else {
        echo "⚠️  Fichier source non trouvé : $source\n";
    }
}

// Création d'un contrôleur et d'une vue d'exemple
$exampleControllerContent = <<<'PHP'
<?php

namespace App\Controller;

use Mini\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function index()
    {
        return $this->viewRenderer->render('home.phtml', [
            'title' => 'Bienvenue sur votre nouveau projet MVC-Starter'
        ]);
    }
}
PHP;

$exampleViewContent = <<<'HTML'
<div class="container mt-5">
    <div class="jumbotron">
        <h1><?= $title ?></h1>
        <p class="lead">Ceci est la page d'accueil générée automatiquement par MVC-Starter.</p>
        <hr class="my-4">
        <p>Pour commencer, modifiez les fichiers dans les dossiers src/ et views/.</p>
    </div>
</div>
HTML;

// Création du contrôleur exemple
$controllerPath = $baseDir . '/src/Controller/HomeController.php';
if (!file_exists($controllerPath)) {
    file_put_contents($controllerPath, $exampleControllerContent);
    echo "✅ Contrôleur d'exemple créé : src/Controller/HomeController.php\n";
}

// Création de la vue exemple
$viewPath = $baseDir . '/views/home.phtml';
if (!file_exists($viewPath)) {
    file_put_contents($viewPath, $exampleViewContent);
    echo "✅ Vue d'exemple créée : views/home.phtml\n";
}

// Création d'un fichier de template pour les messages flash
$flashMessageTemplate = <<<'HTML'
<div class="alert alert-<?= $cssClass ?> alert-dismissible fade show" role="alert">
    <?php foreach (self::getFlash($type) as $message): ?>
        <div><?= $message ?></div>
    <?php endforeach; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
HTML;

$flashMessagePath = $baseDir . '/views/flashMessages/flashMessage.phtml';
if (!file_exists($flashMessagePath)) {
    file_put_contents($flashMessagePath, $flashMessageTemplate);
    echo "✅ Template de message flash créé : views/flashMessages/flashMessage.phtml\n";
}

echo "\n✅ Installation terminée ! 🎉\n";
echo "🚀 Pour lancer le serveur local : php -S localhost:8000 -t public\n";
