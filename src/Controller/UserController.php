<?php

namespace App\Controller;

/**
 * Contrôleur de la partie utilisateur.
 */

use App\Model\UserModel;
use App\Services\ViewRenderer;
use App\Services\AuthService;

class UserController
{
    private UserModel $userModel;

    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->userModel = new UserModel();
    }

    /**
     * Vérifie si un utilisateur est connecté et retourne son ID
     * @return int|null
     */
    private function getCurrentUserId(): ?int
    {
        $user = AuthService::getUser();

        return $user['id'] ?? null;
    }

    /**
     * Redirige vers la page de connexion si l'utilisateur n'est pas connecté.
     */
    private function ensureAuthenticated(): void
    {
        if (!$this->getCurrentUserId()) {
            $this->viewRenderer->addFlash('error', "Vous devez être connecté pour accéder à cette page.");
            header('Location: ' . $this->viewRenderer->url('/auth/connectionForm'));
            exit;
        }
    }

    /**
     * Affiche les informations de l'utilisateur connecté.
     * @return void
     */
    public function profile(): void
    {
        $this->ensureAuthenticated();
        $userId = $this->getCurrentUserId();
        $user = $this->userModel->findUserById($userId);

        if (!$user) {
            $this->viewRenderer->addFlash('error', "Utilisateur introuvable.");
            header('Location: ' . $this->viewRenderer->url('/articles'));
            exit;
        }

        $data = [
            'title' => 'Mon profil',
            'user' => $user
        ];
        $this->viewRenderer->render('../views/users/profile.phtml', $data);
    }

    /**
     * Affiche le formulaire pour modifier ses informations.
     * @return void
     */
    public function editProfile(): void
    {
        $this->ensureAuthenticated();
        $userId = $this->getCurrentUserId();
        $user = $this->userModel->findUserById($userId);

        if (!$user) {
            $this->viewRenderer->addFlash('error', "Utilisateur introuvable.");
            header('Location: ' . $this->viewRenderer->url('/auth/connectionForm'));
            exit;
        }

        $data = [
            'title' => 'Modifier mon profil',
            'user' => $user
        ];
        $this->viewRenderer->render('../views/users/edit.phtml', $data);
    }

    /**
     * Met à jour les informations de l'utilisateur connecté.
     * @return void
     */
    public function updateProfile(): void
    {
        $this->ensureAuthenticated();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $this->getCurrentUserId();
            $nickname = trim($_POST['nickname']);
            $email = trim($_POST['email']);

            if (empty($nickname)) {
                $this->viewRenderer->addFlash('error', "Le pseudonyme est requis.");
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->viewRenderer->addFlash('error', "Veuillez saisir un email valide.");
            }

            if ($this->viewRenderer->hasFlash('error')) {
                header('Location: ' . $this->viewRenderer->url('/users/editProfile'));
                exit;
            }

            $this->userModel->updateUser($userId, [
                'nickname' => $nickname,
                'email' => $email,
            ]);

            $this->viewRenderer->addFlash('success', "Profil mis à jour avec succès.");
            header('Location: ' . $this->viewRenderer->url('/users/profile'));
            exit;
        }
    }

    /**
     * Supprime le compte de l'utilisateur connecté.
     * @return void
     */
    public function deleteAccount(): void
    {
        $this->ensureAuthenticated();
        $userId = $this->getCurrentUserId();
        $user = $this->userModel->findUserById($userId);

        if (!$user) {
            $this->viewRenderer->addFlash('error', "Utilisateur introuvable.");
            header('Location: ' . $this->viewRenderer->url('/users/profile'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->userModel->deleteUser($userId)) {
                // Déconnecte l'utilisateur après suppression
                AuthService::logout();
                $this->viewRenderer->addFlash('success', "Votre compte a été supprimé.");
                header('Location: ' . $this->viewRenderer->url('/articles'));
                exit;
            }
        }

        $this->viewRenderer->addFlash('error', "Impossible de supprimer votre compte.");
        header('Location: ' . $this->viewRenderer->url('/users/profile'));
        exit;
    }
}
