<?php

namespace Metroid\Services;

/**
 * Utilitaire pour la gestion de l'authentification.
 */
class AuthService
{
    /**
     * Initialise la session si elle n'est pas démarrée.
     */
    public static function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Stocke les informations de l'utilisateur en session.
     */
    public static function login(array $user): void
    {
        self::initSession();
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nickname' => $user['nickname'],
            'email' => $user['email'],
            'is_admin' => $user['is_admin']
        ];
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public static function logout(): void
    {
        self::initSession();
        unset($_SESSION['user']);
        session_destroy();
    }

    /**
     * Vérifie si l'utilisateur est connecté.
     */
    public static function isAuthenticated(): bool
    {
        self::initSession();
        return isset($_SESSION['user']);
    }

    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public static function isAdmin(): bool
    {
        self::initSession();
        return isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] == true;
    }

    /**
     * Récupère les informations de l'utilisateur connecté.
     */
    public static function getUser(): ?array
    {
        self::initSession();
        return $_SESSION['user'] ?? null;
    }
}
