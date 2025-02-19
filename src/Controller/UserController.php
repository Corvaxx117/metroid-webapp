<?php

namespace App\Controller;

/**
 * Contrôleur de la partie utilisateur.
 */

use App\Model\UserModel;
use App\Services\ViewRenderer;
use App\Services\FlashMessage;
use App\Services\UrlGenerator;


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
            FlashMessage::add(FlashMessage::ERROR, "Utilisateur introuvable.");
            header('Location: ' . UrlGenerator::getUrlFromPath('/users'));
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
                FlashMessage::add(FlashMessage::ERROR, "Le nom d'utilisateur est requis.");
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                FlashMessage::add(FlashMessage::ERROR, "Veuillez saisir un email valide.");
            }

            if (FlashMessage::has(FlashMessage::ERROR)) {
                header('Location: ' . UrlGenerator::getUrlFromPath('/users/edit/' . $id));
                exit;
            }

            $this->userModel->updateUser($id, [
                'username' => $username,
                'email' => $email,
            ]);

            FlashMessage::add(FlashMessage::SUCCESS, "Utilisateur mis à jour avec succès.");

            header('Location: ' . UrlGenerator::getUrlFromPath('/users'));
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
            FlashMessage::add(FlashMessage::ERROR, "Utilisateur introuvable.");
            header('Location: ' . UrlGenerator::getUrlFromPath('/users'));
            exit;
        }

        $data = [
            'title' => 'Détails de l\'utilisateur',
            'user' => $user
        ];
        $this->viewRenderer->render('../views/users/show.phtml', $data);
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
                FlashMessage::add(FlashMessage::ERROR, "Le nom d'utilisateur est requis.");
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                FlashMessage::add(FlashMessage::ERROR, "Veuillez saisir un email valide.");
            }

            if (empty($password) || strlen($password) < 6) {
                FlashMessage::add(FlashMessage::ERROR, "Le mot de passe doit contenir au moins 6 caractères.");
            }

            if ($this->userModel->findUserByEmail($email)) {
                FlashMessage::add(FlashMessage::ERROR, "Cet email est déjà utilisé.");
            }

            if (FlashMessage::has(FlashMessage::ERROR)) {
                header('Location: ' . UrlGenerator::getUrlFromPath('/users/addUser'));
                exit;
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $this->userModel->createUser([
                'nickname' => $username,
                'email' => $email,
                'password' => $passwordHash
            ]);

            FlashMessage::add(FlashMessage::SUCCESS, "Utilisateur ajouté avec succès.");

            // Redirection vers la liste des utilisateurs
            header('Location: '  . UrlGenerator::getUrlFromPath('/users'));
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
            FlashMessage::add(FlashMessage::ERROR, "Utilisateur introuvable.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->userModel->deleteUser($id)) {
                FlashMessage::add(FlashMessage::SUCCESS, "Utilisateur supprimé avec succès.");
                header('Location: ' . UrlGenerator::getUrlFromPath('/users'));
                exit;
            }
        }

        FlashMessage::add(FlashMessage::ERROR, "Impossible de supprimer cet utilisateur.");
        header('Location: '  . UrlGenerator::getUrlFromPath('/users'));
        exit;
    }
}
