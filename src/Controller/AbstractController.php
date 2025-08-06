<?php

namespace Metroid\Controller;

use Metroid\Http\Response;
use Metroid\View\ViewRenderer;
use Metroid\Http\RedirectResponse;
use Metroid\FlashMessage\FlashMessage;
use Metroid\Services\AuthService;


abstract class AbstractController
{
    public function __construct(
        readonly protected ViewRenderer $viewRenderer,
        readonly protected FlashMessage $flashMessage
    ) {

        $this->init();
    }

    protected function init(): void {}

    /**
     * Raccourci pour rendre une vue avec Response directement.
     */
    protected function render(string $view, array $data = [], int $statusCode = 200, array $headers = []): Response
    {
        return $this->viewRenderer->render($view, $data, $statusCode, $headers);
    }

    /**
     * Effectue une redirection HTTP.
     *
     * @param string $url L'URL de redirection. Si relative, elle sera résolue par rapport à l'URL actuelle.
     * @param int $statusCode Le code d'état HTTP de la redirection (par défaut 302).
     *
     * @return RedirectResponse La réponse de redirection.
     */
    protected function redirect(string $url, int $statusCode = 302): RedirectResponse
    {
        $resolvedUrl = $this->viewRenderer->url($url);
        return new RedirectResponse($resolvedUrl, $statusCode);
    }

    /**
     * Redirige vers une page de connexion si l'utilisateur n'est pas connecté.
     *
     * @param string $redirectUrl L'URL de redirection. Si relative, elle sera résolue par rapport à l'URL actuelle.
     * @return ?Response La réponse de redirection si l'utilisateur n'est pas connecté, null sinon.
     */
    protected function requireAuthentication(string $redirectUrl = '/auth/login'): ?Response
    {
        if (!AuthService::isAuthenticated()) {
            $this->flashMessage->add('error', 'Vous devez être connecté.');
            return $this->redirect($redirectUrl);
        }

        return null;
    }
}
