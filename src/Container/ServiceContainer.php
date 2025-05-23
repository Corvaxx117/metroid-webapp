<?php

namespace Metroid\Container;

use Metroid\View\ViewRenderer;
use Metroid\Services\UrlGenerator;
use Metroid\Services\TextHandler;
use Metroid\Services\FormatToFrenchDate;
use Metroid\Services\SortHelper;
use Metroid\FlashMessage\FlashMessage;
use Metroid\Services\AuthService;
use Metroid\Http\Request;

/**
 * Classe responsable de la gestion des services
 */
class ServiceContainer
{
    private array $services = [];

    public function __construct()
    {
        $this->registerCoreServices();
    }

    /**
     * Enregistre les services de base.
     */
    private function registerCoreServices(): void
    {
        $this->set(Request::class, fn() => new Request());
        $this->set(UrlGenerator::class, fn() => new UrlGenerator());
        $this->set(TextHandler::class, fn() => new TextHandler());
        $this->set(FormatToFrenchDate::class, fn() => new FormatToFrenchDate());
        $this->set(AuthService::class, fn() => new AuthService());
        $this->set(FlashMessage::class, fn() => new FlashMessage());
        $this->set(SortHelper::class, fn() => new SortHelper());

        $this->set(ViewRenderer::class, fn(ServiceContainer $c) => new ViewRenderer(
            $c->get(UrlGenerator::class),
            $c->get(TextHandler::class),
            $c->get(FormatToFrenchDate::class),
            $c->get(FlashMessage::class),
            $c->get(AuthService::class),
            $c->get(SortHelper::class)
        ));
    }

    /**
     * Enregistre un service.
     *
     * Le service est stocké dans un tableau avec comme clé  l'ID du service.
     * Si l'ID du service existe déjà , il sera écrasé .
     *
     * @param string $id ID du service
     * @param \Closure $factory Closure qui instancie le service
     */
    public function set(string $id, \Closure $factory): void
    {
        $this->services[$id] = $factory;
    }

    /**
     * Retourne un service.
     *
     * Si le service n'est pas encore instancié, il sera instancié en appelant
     * la Closure enregistrée pour ce service.
     *
     * @param string $id ID du service
     *
     * @throws \RuntimeException Si le service n'est pas enregistré
     *
     * @return object Instance du service
     */
    public function get(string $id): object
    {
        if (!isset($this->services[$id])) {
            throw new \RuntimeException("Le service '{$id}' n'est pas enregistré.");
        }

        if ($this->services[$id] instanceof \Closure) {
            // On appelle la Closure et on remplace par l'instance
            $this->services[$id] = ($this->services[$id])($this);
        }

        return $this->services[$id];
    }
}
