<?php

namespace Metroid\FlashMessage\Handler;

class SessionHandler
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
    }

    /**
     * Ajoute un message flash à la session.
     * @param string $type Le type de message (error, success, warning).
     * @param string $message Le message à ajouter.
     * @return void
     */
    public function add(string $type, string $message): void
    {
        $_SESSION['flash'][$type][] = $message;
    }

    /**
     * Récupère et supprime les messages d'un type donné.
     * @param string $type Le type de message (error, success, warning).
     * @return array Les messages flash.
     */
    public function get(string $type): array
    {
        $messages = $_SESSION['flash'][$type] ?? [];
        unset($_SESSION['flash'][$type]);
        return $messages;
    }

    /**
     * Vérifie si des messages existent pour un type donné.
     * @param string $type Le type de message (error, success, warning).
     * @return bool true si des messages existent, false sinon.
     */
    public function has(string $type): bool
    {
        return !empty($_SESSION['flash'][$type]);
    }

    /**
     * Supprime tous les messages flash.
     * @return void
     */
    public function clearFlash(): void
    {
        unset($_SESSION['flash_messages']);
    }

    /**
     * Affiche les messages flash avec un système dynamique.
     * @return void
     */
    public function renderFlash(): void
    {
        foreach (['success', 'error', 'warning'] as $type) {
            if (!empty($_SESSION['flash'][$type])) {
                $messages = $_SESSION['flash'][$type];
                unset($_SESSION['flash'][$type]);

                $cssClass = match ($type) {
                    'success' => 'success',
                    'error'   => 'danger',
                    'warning' => 'warning',
                    default   => ''
                };

                foreach ($messages as $message) {
                    // Inclure un template flash pour chaque message
                    include VIEW_PATH . 'flashMessage.phtml';
                }
            }
        }
    }
}
