<?php

namespace App\Controller;

/**
 * Contrôleur de la partie utilisateur.
 */

use App\Model\UserModel;
use App\Services\ViewRenderer;


class UserController
{
    private UserModel $userModel;

    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->userModel = new UserModel();
    }

    /**
     * Affiche la liste des utilisateurs.
     * @return void
     */
    public function list(): void
    {
        $users = $this->userModel->findAllUsers();
        $data = [
            'title' => 'Liste des utilisateurs',
            'users' => $users
        ];
        $this->viewRenderer->render('../views/users/list.phtml', $data);
    }

    /**
     * Affiche le formulaire pour modifier les données d’un utilisateur.
     * @param int $id L'identifiant de l'utilisateur
     * @return void
     */
    public function editUser($id): void
    {
        // Récupérer l'utilisateur depuis la base de données
        $user = $this->userModel->findUserById($id);
        if (!$user) {
            // Rediriger ou afficher un message d'erreur si l'utilisateur est introuvable
            $this->viewRenderer->addFlash('error', "Utilisateur introuvable.");
            header('Location: ' . $this->viewRenderer->url('/users'));
            exit;
        }
        $data = [
            'title' => 'Modifier les données de l\'utilisateur',
            'user' => $user
        ];
        $this->viewRenderer->render('../views/users/edit.phtml', $data);
    }
    /**
     * Met à jour un utilisateur.
     * @return void
     */
    public function updateUser(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);

            if (empty($username)) {
                $this->viewRenderer->addFlash('error', "Le nom d'utilisateur est requis.");
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->viewRenderer->addFlash('error', "Veuillez saisir un email valide.");
            }

            if ($this->viewRenderer->has('error')) {
                header('Location: ' . $this->viewRenderer->url('/users/edit/' . $id));
                exit;
            }

            $this->userModel->updateUser($id, [
                'username' => $username,
                'email' => $email,
            ]);

            $this->viewRenderer->addFlash('success', "Utilisateur mis à jour avec succès.");

            header('Location: ' . $this->viewRenderer->url('/users'));
            exit;
        }
    }

    /**
     * Affiche les détails d’un utilisateur.
     * @param int $id L'identifiant de l'utilisateur
     * @return void
     */
    public function show(int $id): void
    {
        $user = $this->userModel->findUserById($id);

        if (!$user) {
            // Envoie une erreur si l'utilisateur n'est pas trouvé
            $this->viewRenderer->addFlash('error', "Utilisateur introuvable.");
            header('Location: ' . $this->viewRenderer->url('/users'));
            exit;
        }

        $data = [
            'title' => 'Détails de l\'utilisateur',
            'user' => $user
        ];
        $this->viewRenderer->render('../views/users/show.phtml', $data);
    }

    /**
     * Affichage du formulaire d'ajout d'un utilisateur'.
     * @return void
     */
    public function displayAddUserForm(): void
    {
        $this->viewRenderer->render('../views/users/add.phtml');
    }
    /**
     * Ajoute un nouvel utilisateur.
     * @return void
     */
    public function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = trim($_POST['nickname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username)) {
                $this->viewRenderer->addFlash('error', "Le nom d'utilisateur est requis.");
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->viewRenderer->addFlash('error', "Veuillez saisir un email valide.");
            }

            if (empty($password) || strlen($password) < 6) {
                $this->viewRenderer->addFlash('error', "Le mot de passe doit contenir au moins 6 caractères.");
            }

            if ($this->userModel->findUserByEmail($email)) {
                $this->viewRenderer->addFlash('error', "Cet email est déjà utilisé.");
            }

            if ($this->viewRenderer->hasFlash('error')) {
                header('Location: ' . $this->viewRenderer->url('/users/addUser'));
                exit;
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $this->userModel->createUser([
                'nickname' => $username,
                'email' => $email,
                'password' => $passwordHash
            ]);

            $this->viewRenderer->addFlash('success', "Utilisateur ajouté avec succès.");

            // Redirection vers la liste des utilisateurs
            header('Location: '  . $this->viewRenderer->url('/users'));
            exit;
        }

        $this->viewRenderer->render('../views/users/add.phtml', ['title' => 'Ajouter un utilisateur']);
    }

    /**
     * Supprimer un utilisateur.
     * @param int $id L'identifiant de l'utilisateur
     */
    public function delete(int $id): void
    {
        $user = $this->userModel->findUserById($id);
        if (!$user) {
            $this->viewRenderer->addFlash('error', "Utilisateur introuvable.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->userModel->deleteUser($id)) {
                $this->viewRenderer->addFlash('success', "Utilisateur supprimé avec succès.");
                header('Location: ' . $this->viewRenderer->url('/users'));
                exit;
            }
        }

        $this->viewRenderer->addFlash('error', "Impossible de supprimer cet utilisateur.");
        header('Location: '  . $this->viewRenderer->url('/users'));
        exit;
    }
}
