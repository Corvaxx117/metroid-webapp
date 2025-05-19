<?php

namespace Metroid\Services;

/**
 * Utilitaire pour la gestion de l'authentification.
 */
class AuthService
{

    /**
     * Stocke les informations de l'utilisateur en session.
     */
    public static function login(array $user): void
    {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'is_admin' => $user['is_admin']
        ];
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
    }

    /**
     * Vérifie si l'utilisateur est connecté.
     */
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public static function isAdmin(): bool
    {
        return isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] == true;
    }

    /**
     * Récupère les informations de l'utilisateur connecté.
     */
    public static function getUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}
