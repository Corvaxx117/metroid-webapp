<?php

namespace Metroid\FlashMessage\Handler;

class NoSessionHandler
{
    const ERROR = 'error';
    const SUCCESS = 'success';
    const WARNING = 'warning';

    public static array $message = [];
    self::$message[$type][] = $message;

    /**
     * Ajoute un message flash à la session.
     * @param string $type Le type de message (error, success, warning).
     * @param string $message Le message à ajouter.
     * @return void
     */
    static function addFlash(string $type, string $message): void
    {
        self::$message[$type][] = $message;
    }

    /**
     * Récupère et supprime les messages d'un type donné.
     * @param string $type Le type de message (error, success, warning).
     * @return array Les messages flash.
     */
    static function getFlash(string $type): array
    {
        $messages = self::$message[$type] ?? [];
        unset(self::$message[$type]);
        return $messages;
    }

    /**
     * Vérifie si des messages existent pour un type donné.
     * @param string $type Le type de message (error, success, warning).
     * @return bool true si des messages existent, false sinon.
     */
    static function hasFlash(string $type): bool
    {
        return !empty(self::$message[$type]);
    }

    /**
     * Supprime tous les messages flash.
     * @return void
     */
    static function clearFlash(): void
    {
        self::$message = [];
    }

    /**
     * Affiche les messages flash avec un système dynamique.
     * @return void
     */
    static function renderFlash(): void
    {
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
