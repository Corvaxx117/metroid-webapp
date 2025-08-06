<?php

namespace Metroid\Services;

use App\Security\User;

/**
 * Utilitaire pour la gestion de l'authentification.
 */
class AuthService
{

    /**
     * Stocke les informations de l'utilisateur en session.
     */
    public static function login(object $user): void
    {
        $_SESSION['user'] = $user;
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public static function logout(): void
    {
        // Supprimer la variable d'utilisateur si elle existe
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }

        // Vérifie si une session est active avant de la détruire
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
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
    public static function getUser(): ?User
    {
        return $_SESSION['user'] ?? null;
    }
}
