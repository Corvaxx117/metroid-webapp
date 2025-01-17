<?php

namespace App\Services;

use App\Core\ErrorHandler;

class ViewRenderer
{
    public function render(string $view, array $data = [], int $statusCode = 200): void
    {
        // Défini le code HTTP
        http_response_code($statusCode);

        // Vérifie si le fichier de vue existe
        $viewPath = __DIR__ . "/../../views/{$view}";
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Le fichier '{$view}' est introuvable.");
        }
        // Chemin du fichier de mise en page (layout)
        $layoutPath = __DIR__ . "/../../views/layout.phtml";
        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Le fichier de mise en page est introuvable.");
        }
        // Extrait les données à inclure dans la vue
        extract($data);


        // Définir $template pour inclure la vue dans le layout
        $template = $viewPath;

        // Inclure le fichier layout
        require $layoutPath;
    }
}
