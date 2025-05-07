<?php

namespace Metroid\View;

use Metroid\Services\UrlGenerator;
use Metroid\Services\TextHandler;
use Metroid\Services\FormatToFrenchDate;
use Metroid\FlashMessage\FlashMessage;
use Metroid\Services\AuthService;

/**
 * Class ViewRenderer
 * Gestionnaire de vue
 * Charge la plupart des services et permet de passer des données à la vue
 * @package Metroid\Services
 */
class ViewRenderer
{
    private UrlGenerator $url;
    private TextHandler $textHandler;
    private FormatToFrenchDate $formatDate;
    private FlashMessage $flashMessage;
    private AuthService $auth;
    public function __construct()
    {
        $this->url = new UrlGenerator();
        $this->textHandler = new TextHandler();
        $this->formatDate = new FormatToFrenchDate();
        $this->flashMessage = new FlashMessage();
        $this->auth = new AuthService();
    }

    // methode appelée automatiquement dès lors qu'on appelle une méthode inexistante dans l'objet ou  dans l'instance de la classe
    // Parcours toutes les propriétés de l'objet
    // Teste si la méthode que l'on tente d'appeler existe
    // si elle existe, on appelle la methode
    // Sinon par defaut, on appelle la  méthode invoque
    public function __call($name, $arguments)
    {
        // retourne la liste des propriétés accessibles de l'objet en argument
        // ici $url et $textHandler sous forme de tableau
        // voir doc __invoque, ..., get_object_vars, is_callable
        $properties = get_object_vars($this);
        foreach ($properties as $propertyValue) {
            if (is_callable([$propertyValue, $name])) {
                return $propertyValue->$name(...$arguments);
            }
        }
        return ($this->$name)(...$arguments);
    }
    public function render(string $view, array $data = [], int $statusCode = 200, bool $capture = false): ?string
    {
        // Défini le code HTTP
        http_response_code($statusCode);

        // Vérifie si le fichier de vue existe
        $viewPath = VIEW_PATH . $view;
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Le fichier '{$view}' est introuvable.");
        }
        // Chemin du fichier de mise en page (layout)
        $layoutPath = VIEW_PATH . 'layout.phtml';
        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Le fichier de mise en page est introuvable.");
        }
        // Extrait les données à inclure dans la vue
        extract($data);

        // Définir $template pour inclure la vue dans le layout
        $template = $viewPath;

        // Inclure le fichier layout
        if ($capture) {
            ob_start();
            require $layoutPath;
            return ob_get_clean();
        }

        require $layoutPath;
        return null;
    }
}
