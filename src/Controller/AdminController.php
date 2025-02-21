<?php

namespace App\Controller;

use App\Model\ArticleModel;
use App\Model\UserModel;
use App\Services\ViewRenderer;

/**
 * Contrôleur de la partie admin.
 */
class AdminController
{
    private ArticleModel $articleModel;
    private UserModel $userModel;

    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->articleModel = new ArticleModel();
        $this->userModel = new UserModel();
    }
    /**
     * Affiche la page d'administration.
     * @return void
     */
    public function showAdmin(): void
    {
        // On vérifie que l'utilisateur est connecté.
        $this->checkIfUserIsConnected();

        // On récupère les articles.
        $articles = $this->articleModel->getAllArticles();

        // On affiche la page d'administration.
        $data = [
            'articles' => $articles
        ];
        $this->viewRenderer->render('../views/admin.phtml', $data);
    }

    /**
     * Vérifie que l'utilisateur est connecté.
     * @return void
     */
    private function checkIfUserIsConnected(): void
    {
        // On vérifie que l'utilisateur est connecté.
        if (!isset($_SESSION['user'])) {
            $this->viewRenderer->url("/connectionForm");
        }
    }

    /**
     * Affichage du formulaire de connexion.
     * @return void
     */
    public function displayConnectionForm(): void
    {
        $this->viewRenderer->render('../views/admin/connectionForm.phtml');
    }

    /**
     * Connexion de l'utilisateur.
     * @return void
     */
    public function connect(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (empty($email) || empty($password)) {
                $this->viewRenderer->addFlash('error', "Tous les champs sont obligatoires.");
                header('Location: ' . $this->viewRenderer->url('/admin/connectionForm'));
                exit;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->viewRenderer->addFlash('error', "Veuillez saisir un email valide.");
            }
            // On vérifie que l'utilisateur existe.
            $user = $this->userModel->findUserByEmail($email);
            if (!$user) {
                $this->viewRenderer->addFlash('error', "L'utilisateur demandé n'existe pas.");
                header('Location: ' . $this->viewRenderer->url('/admin/connectionForm'));
                exit;
            }

            // On vérifie que le mot de passe est correct.
            if (!password_verify($password, $user['password'])) {
                $this->viewRenderer->addFlash('error', "Le mot de passe est incorrect.");
                header('Location: ' . $this->viewRenderer->url('/admin/connectionForm'));
                exit;
            }

            // On connecte l'utilisateur.
            $_SESSION['user'] = $user;
            $_SESSION['idUser'] = $user['id'];

            $users = $this->userModel->findAllUsers();
            $articles = $this->articleModel->getAllArticles();
            $data = [
                'users' => $users,
                'articles' => $articles

            ];
            // On redirige vers la page d'administration.
            $this->viewRenderer->render('../views/admin/admin.phtml', $data);
        }
    }

    /**
     * Déconnexion de l'utilisateur.
     * @return void
     */
    public function disconnect(): void
    {
        // On déconnecte l'utilisateur.
        unset($_SESSION['user']);
        $articles = $this->articleModel->getAllArticles();
        $data = [
            'title' => 'Liste des articles',
            'articles' => $articles
        ];
        $this->viewRenderer->addFlash('warning', "Vous êtes maintenant déconnecté.");
        // On redirige vers la page d'accueil.
        $this->viewRenderer->render('../views/articles/list.phtml', $data);
    }

    /**
     * Affichage du formulaire d'ajout d'un article.
     * @return void
     */
    public function showUpdateArticleForm(): void
    {
        $this->checkIfUserIsConnected();

        // // On récupère l'id de l'article s'il existe.
        // $id = Utils::request("id", -1);

        // // On récupère l'article associé.
        // $articleManager = new ArticleManager();
        // $article = $articleManager->getArticleById($id);

        // // Si l'article n'existe pas, on en crée un vide. 
        // if (!$article) {
        //     $article = new Article();
        // }

        // // On affiche la page de modification de l'article.
        // $view = new View("Edition d'un article");
        // $view->render("updateArticleForm", [
        //     'article' => $article
        // ]);
    }

    /**
     * Ajout et modification d'un article. 
     * On sait si un article est ajouté car l'id vaut -1.
     * @return void
     */
    public function updateArticle(): void
    {
        // $this->checkIfUserIsConnected();

        // // On récupère les données du formulaire.
        // $id = Utils::request("id", -1);
        // $title = Utils::request("title");
        // $content = Utils::request("content");

        // // On vérifie que les données sont valides.
        // if (empty($title) || empty($content)) {
        //     throw new Exception("Tous les champs sont obligatoires. 2");
        // }

        // // On crée l'objet Article.
        // $article = new Article([
        //     'id' => $id, // Si l'id vaut -1, l'article sera ajouté. Sinon, il sera modifié.
        //     'title' => $title,
        //     'content' => $content,
        //     'id_user' => $_SESSION['idUser']
        // ]);

        // // On ajoute l'article.
        // $articleManager = new ArticleManager();
        // $articleManager->addOrUpdateArticle($article);

        // // On redirige vers la page d'administration.
        // Utils::redirect("admin");
    }


    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle(): void
    {
        // $this->checkIfUserIsConnected();

        // $id = Utils::request("id", -1);

        // // On supprime l'article.
        // $articleManager = new ArticleManager();
        // $articleManager->deleteArticle($id);

        // // On redirige vers la page d'administration.
        // Utils::redirect("admin");
    }
}
