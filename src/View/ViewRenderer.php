<?php

namespace Metroid\View;

use Metroid\Services\UrlGenerator;
use Metroid\Services\TextHandler;
use Metroid\Services\FormatToFrenchDate;
use Metroid\Services\SortHelper;
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
        private AuthService $auth,
        private SortHelper $sort
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

    /**
     * Génère une URL complète à partir d'un chemin donné.
     * 
     * @param string $path Chemin relatif pour lequel générer l'URL.
     * @param array $params Tableau de paramètres à injecter dans l'URL.
     * 
     * @return string URL complète générée.
     */
    public function url(string $path = '', array $params = []): string
    {
        return $this->url->getUrlFromPath($path, $params);
    }

    /**
     * Protège une chaine contre les attaques XSS et formate les retours à la ligne.
     *
     * @param string|null $value La chaine à protéger.
     * @param bool $wrapInParagraphs Si true, chaque ligne est entourée de <p>...</p>.
     *
     * @return string La chaine protégée et formatée.
     */
    public function clean(string $value = null, bool $wrapInParagraphs = true): string
    {
        return $this->textHandler->clean($value, $wrapInParagraphs);
    }

    /**
     * Formate une date pour qu'elle soit lisible en français.
     *
     * @param string $datetime La date à formater.
     *
     * @return string La date formatée.
     */
    public function formatDate(string $datetime): string
    {
        return $this->formatDate->formatDate($datetime);
    }

    /**
     * Affiche les messages flash. Les messages flash sont des messages temporaires
     * qui s'affichent après une action. Ils sont stockés en session.
     *
     * @return void
     */
    public function renderFlash(): void
    {
        $this->flashMessage->renderFlash();
    }

    /**
     * Vérifie si l'utilisateur est authentifié.
     *
     * @return bool Retourne true si l'utilisateur est authentifié, false sinon.
     */

    public function isAuthenticated(): bool
    {
        return $this->auth->isAuthenticated();
    }

    /**
     * Retourne true si l'utilisateur est administrateur, false sinon.
     *
     * @return bool L'utilisateur est-il administrateur ?
     */
    public function isAdmin(): bool
    {
        return $this->auth->isAdmin();
    }

    /**
     * Change le sens de tri pour un champ donné.
     *
     * @param string $field Champ sur lequel on veut trier
     * @return string Nouvel ordre de tri ('ASC' ou 'DESC')
     */

    public function toggleSort(string $field): string
    {
        return $this->sort->toggleSort($field);
    }

    /**
     * Retourne le symbole ascendant ou descendant en fonction de l'ordre de tri
     * actuel et du champ demandé.
     *
     * @param string $field Le champ sur lequel on veut afficher l'indicateur de tri.
     * @return string Le symbole ascendant (▲) ou descendant (▼).
     */
    public function sortIcon(string $field): string
    {
        return $this->sort->sortIcon($field);
    }

    /**
     * Tronque le texte après un certain nombre de mots.
     *
     * @param string $content Le contenu à tronquer.
     * @param int $wordLimit Le nombre de mots maximum.
     * @return string Le contenu tronqué.
     */
    public function truncate(string $content, int $wordLimit = 30): string
    {
        return $this->textHandler->truncate($content, $wordLimit);
    }
}
