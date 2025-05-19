<?php

namespace Metroid\View;

use Metroid\Services\UrlGenerator;
use Metroid\Services\TextHandler;
use Metroid\Services\FormatToFrenchDate;
use Metroid\FlashMessage\FlashMessage;
use Metroid\Services\AuthService;
use Metroid\Http\Response;

class ViewRenderer
{
    public function __construct(
        private UrlGenerator $url,
        private TextHandler $textHandler,
        private FormatToFrenchDate $formatDate,
        private FlashMessage $flashMessage,
        private AuthService $auth
    ) {}

    public function render(string $view, array $data = [], int $statusCode = 200, array $headers = []): Response
    {
        http_response_code($statusCode);

        $viewPath = VIEW_PATH . $view;
        $layoutPath = VIEW_PATH . 'layout.phtml';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Le fichier de vue '{$view}' est introuvable.");
        }

        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Le fichier de layout est introuvable.");
        }

        extract($data);
        $template = $viewPath;

        ob_start();
        require $layoutPath;
        $html = ob_get_clean();

        return (new Response())
            ->setContent($html)
            ->setStatusCode($statusCode)
            ->setHeaders($headers);
    }

    // Méthodes exposées explicitement aux vues
    public function url(string $path = ''): string
    {
        return $this->url->getUrlFromPath($path);
    }

    public function clean(string $value = null, bool $wrapInParagraphs = true): string
    {
        return $this->textHandler->clean($value, $wrapInParagraphs);
    }

    public function formatDate(string $datetime): string
    {
        return $this->formatDate->formatDate($datetime);
    }

    public function renderFlash(): void
    {
        $this->flashMessage->renderFlash();
    }

    public function isAuthenticated(): bool
    {
        return $this->auth->isAuthenticated();
    }

    public function isAdmin(): bool
    {
        return $this->auth->isAdmin();
    }
}
