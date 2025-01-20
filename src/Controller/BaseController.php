<?php

namespace App\Controller;

use App\Services\ViewRenderer;

abstract class BaseController
{
    public function __construct(protected ViewRenderer $viewRenderer) {}



    // En faire un Trait et l'appeler aux endroits nécéssaires pour la gestion des erreurs
    /**
     * Gère les erreurs utilisateur et les messages de validation
     */
    protected function renderError(
        array $errors = [],
        array $valids = [],
        string $view = null,
        array $data = []
    ): void {
        $data['errors'] = $errors;
        $data['valids'] = $valids;

        // Inclure les vues errors.phtml et valids.phtml si des messages sont présents
        // Declarer le buffer pour stocker provisoirement les messages
        ob_start();
        if (!empty($errors)) {
            $this->viewRenderer->render('errors.phtml', $data);
        }
        if (!empty($valids)) {
            $this->viewRenderer->render('valids.phtml', $data);
        }

        // Inclure une vue principale si spécifiée
        if ($view) {
            $this->viewRenderer->render($view, $data);
        }
        // Envoyer au navigateur et fermer le buffer
        ob_end_flush();
    }
}
