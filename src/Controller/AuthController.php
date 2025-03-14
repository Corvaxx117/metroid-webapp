<?php

namespace App\Controller;

use App\Model\UserModel;
use App\Services\ViewRenderer;
use App\Services\AuthService;

class AuthController
{
    private UserModel $userModel;
    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->userModel = new UserModel();
    }

    /**
     * Affichage du formulaire de connexion administrateur.
     * @return void
     */
    public function displayRegistrationForm(): void
    {
        $this->viewRenderer->render('auth/registration_form.phtml');
    }

    /**
     * Inscription de l'utilisateur.
     * @return void
     */

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nickname = trim($_POST['nickname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');

            if (empty($nickname) || empty($email) || empty($password) || empty($confirmPassword)) {
                $this->viewRenderer->addFlash('error', "Tous les champs sont obligatoires.");
                header('Location: ' . $this->viewRenderer->url('auth/registrationForm'));
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->viewRenderer->addFlash('error', "Veuillez saisir un email valide.");
                header('Location: ' . $this->viewRenderer->url('auth/registrationForm'));
                exit;
            }

            if ($password !== $confirmPassword) {
                $this->viewRenderer->addFlash('error', "Les mots de passe ne correspondent pas.");
                header('Location: ' . $this->viewRenderer->url('auth/registrationForm'));
                exit;
            }

            // Vérification utilisateur
            $user = $this->userModel->findUserByEmail($email);
            if ($user) {
                $this->viewRenderer->addFlash('error', "Un utilisateur avec cet email existe deja.");
                header('Location: ' . $this->viewRenderer->url('auth/registrationForm'));
                exit;
            }
            $data = [
                'nickname' => $nickname,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ];

            // Création de l'utilisateur
            $this->userModel->createUser($data);

            $this->viewRenderer->addFlash('success', "Inscription réussie.");
            header('Location: ' . $this->viewRenderer->url('auth/connectionForm'));
            exit;
        }
    }

    /**
     * Affichage du formulaire de connexion administrateur.
     */
    public function displayConnectionForm(): void
    {
        $this->viewRenderer->render('auth/connection_form.phtml');
    }

    /**
     * Connexion de l'utilisateur en tant qu'administrateur.
     */
    public function connect(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                $this->viewRenderer->addFlash('error', "Tous les champs sont obligatoires.");
                header('Location: ' . $this->viewRenderer->url('auth/connectionForm'));
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->viewRenderer->addFlash('error', "Veuillez saisir un email valide.");
                header('Location: ' . $this->viewRenderer->url('auth/connectionForm'));
                exit;
            }

            // Vérification utilisateur
            $user = $this->userModel->findUserByEmail($email);
            if (!$user || !password_verify($password, $user['password'])) {
                $this->viewRenderer->addFlash('error', "Identifiants incorrects.");
                header('Location: ' . $this->viewRenderer->url('auth/connectionForm'));
                exit;
            }

            // Connexion de l'utilisateur
            AuthService::login($user);

            $this->viewRenderer->addFlash('success', "Connexion réussie.");
            header('Location: ' . $this->viewRenderer->url('/articles'));
            exit;
        }
    }

    /**
     * Déconnexion de l'utilisateur.
     */
    public function disconnect(): void
    {
        AuthService::logout();
        $this->viewRenderer->addFlash('warning', "Vous êtes maintenant déconnecté.");
        header('Location: ' . $this->viewRenderer->url('/articles'));
        exit;
    }
}
