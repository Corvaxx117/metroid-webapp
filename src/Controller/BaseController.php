<?php

namespace App\Controller;

class BaseController
{
    // Rendu d'une vue 
    protected function render(string $view, array $data = [], int $statusCode = 200): void
    {
        // Définit le code de statut HTTP de la réponse
        http_response_code($statusCode);
        // Définit le type de contenu de la réponse
        header('Content-Type: text/html');
        // Extraction des variables pour les rendre accessibles dans la vue
        extract($data);
        // Chargement de la vue si existante
        $viewPath = __DIR__ . "/../../views/{$view}";
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            $this->renderError("La page '{$view}' est introuvable.");
        }
    }


    // Centralise l'envoi d'erreurs système
    protected function renderError(string $message, int $statusCode = 200, string $description = ''): void
    {
        http_response_code($statusCode);

        $viewPath = "/../../views/system-errors.phtml";

        if (file_exists($viewPath)) {
            $this->render('system-errors.phtml', [
                'statusCode' => $statusCode,
                'message' => $message,
                'description' => $description
            ]);
        } else {
            // Affiche un message brut si la vue d'erreur est introuvable
            echo "Erreur critique : {$message} (Code : {$statusCode})";
        }
    }
}
