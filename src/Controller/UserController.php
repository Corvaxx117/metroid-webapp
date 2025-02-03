<?php

namespace App\Controller;

use App\Model\UserModel;

class UserController extends BaseController
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct(); // Appelle le constructeur de BaseController
        $this->userModel = new UserModel();
    }

    /**
     * Affiche la liste des utilisateurs.
     */
    public function list(): void
    {
        $users = $this->userModel->findAll();
        $data = [
            'title' => 'Liste des utilisateurs',
            'users' => $users
        ];
        $this->viewRenderer->render('list.phtml', $data);
    }

    /**
     * Affiche les détails d’un utilisateur.
     */
    public function show(int $id): void
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            // Envoie une erreur si l'utilisateur n'est pas trouvé
            $this->renderError();
            return;
        }

        $data = [
            'title' => 'Détails de l\'utilisateur',
            'user' => $user
        ];
        $this->viewRenderer->render('users/show.phtml', $data);
    }

    /**
     * Exemple de méthode d'ajout d'un utilisateur.
     */
    public function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

            $this->userModel->create([
                'username' => $username,
                'email' => $email,
                'password' => $password
            ]);

            // Redirection après ajout
            header('Location: /users');
            exit;
        }

        $this->viewRenderer->render('users/add.phtml', ['title' => 'Ajouter un utilisateur']);
    }

    /**
     * Supprimer un utilisateur.
     */
    public function delete(int $id): void
    {
        if ($this->userModel->delete($id)) {
            // Redirection après suppression
            header('Location: /users');
            exit;
        }

        $this->renderError();
    }
}
