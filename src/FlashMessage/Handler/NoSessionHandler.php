<?php

namespace Metroid\FlashMessage\Handler;

class NoSessionHandler
{
    private array $messages = [];

    /**
     * Ajoute un message flash à la session.
     * @param string $type Le type de message (error, success, warning).
     * @param string $message Le message à ajouter.
     * @return void
     */
    public function add(string $type, string $message): void
    {
        $this->messages[$type][] = $message;
    }

    /**
     * Récupère et supprime les messages d'un type donné.
     * @param string $type Le type de message (error, success, warning).
     * @return array Les messages flash.
     */
    public function get(string $type): array
    {
        $msgs = $this->messages[$type] ?? [];
        unset($this->messages[$type]);
        return $msgs;
    }

    /**
     * Vérifie si des messages existent pour un type donné.
     * @param string $type Le type de message (error, success, warning).
     * @return bool true si des messages existent, false sinon.
     */
    public function has(string $type): bool
    {
        return !empty($this->messages[$type]);
    }

    /**
     * Supprime tous les messages flash.
     * @return void
     */
    static function clear(): void
    {
        self::$message = [];
    }

    /**
     * Affiche les messages flash avec un système dynamique.
     * @return void
     */
    public function renderFlash(): void
    {
        foreach ($this->messages as $type => $messages) {
            foreach ($messages as $message) {
                echo "<div class='flash flash-{$type}'>{$message}</div>";
            }
        }
        $this->messages = [];
    }
}
