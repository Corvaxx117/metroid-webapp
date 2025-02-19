<?php

namespace App\Services;

class FlashMessage
{
    const ERROR = 'error';
    const SUCCESS = 'success';
    const WARNING = 'warning';

    public static function add(string $type, string $message): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION['flash_messages'][$type][] = $message;
    }

    public static function get(string $type): array
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $messages = $_SESSION['flash_messages'][$type] ?? [];

        // Supprime les messages après récupération
        unset($_SESSION['flash_messages'][$type]);

        return $messages;
    }

    public static function has(string $type): bool
    {
        return !empty($_SESSION['flash_messages'][$type]);
    }

    public static function clear(): void
    {
        unset($_SESSION['flash_messages']);
    }

    /**
     * Affiche les messages flash via les templates dédiés
     */
    public static function render(): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (self::has(self::ERROR)) {
            include __DIR__ . '../../../views/flashMessages/error.phtml';
        }

        if (self::has(self::SUCCESS)) {
            include __DIR__ . '../../../views/flashMessages/success.phtml';
        }

        if (self::has(self::WARNING)) {
            include __DIR__ . '../../../views/flashMessages/warning.phtml';
        }
    }
}
