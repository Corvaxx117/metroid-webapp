<?php

namespace Mini\FlashMessage\Handler;

class SessionHandler
{
    const ERROR = 'error';
    const SUCCESS = 'success';
    const WARNING = 'warning';

    /**
     * Démarre la session si elle n'est pas déjà active.
     * @static
     * @return void
     */
    private function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Ajoute un message flash à la session.
     * @param string $type Le type de message (error, success, warning).
     * @param string $message Le message à ajouter.
     * @return void
     */
    public function addFlash(string $type, string $message): void
    {
        self::initSession();
        $_SESSION['flash_messages'][$type][] = $message;
    }

    /**
     * Récupère et supprime les messages d'un type donné.
     * @param string $type Le type de message (error, success, warning).
     * @return array Les messages flash.
     */
    public function getFlash(string $type): array
    {
        self::initSession();
        $messages = $_SESSION['flash_messages'][$type] ?? [];
        unset($_SESSION['flash_messages'][$type]);
        return $messages;
    }

    /**
     * Vérifie si des messages existent pour un type donné.
     * @param string $type Le type de message (error, success, warning).
     * @return bool true si des messages existent, false sinon.
     */
    public function hasFlash(string $type): bool
    {
        self::initSession();
        return !empty($_SESSION['flash_messages'][$type]);
    }

    /**
     * Supprime tous les messages flash.
     * @return void
     */
    public function clearFlash(): void
    {
        self::initSession();
        unset($_SESSION['flash_messages']);
    }

    /**
     * Affiche les messages flash avec un système dynamique.
     * @return void
     */
    public function renderFlash(): void
    {
        self::initSession();
        $flashTypes = [
            self::ERROR => 'danger',
            self::SUCCESS => 'success',
            self::WARNING => 'warning'
        ];

        foreach ($flashTypes as $type => $cssClass) {
            if (self::hasFlash($type)) {
                include __DIR__ . "/../../views/flashMessages/flashMessage.phtml";
            }
        }
    }
}
