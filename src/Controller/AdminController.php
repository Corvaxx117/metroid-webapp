<?php

namespace App\Controller;

use App\Model\ArticleModel;
use App\Services\ViewRenderer;
use App\Services\AuthService;

class AdminController
{
    private ArticleModel $articleModel;

    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->articleModel = new ArticleModel();
    }

    /**
     * Vérifie que l'utilisateur est administrateur avant d'accéder à une page admin.
     */
    private function ensureAdminAccess(): void
    {
        if (!AuthService::isAdmin()) {
            $this->viewRenderer->addFlash('error', "Accès refusé : vous devez être administrateur.");
            header('Location: ' . $this->viewRenderer->url('/connection_form'));
            exit;
        }
    }

    /**
     * Affiche la page d'administration.
     */
    public function showAdmin(): void
    {
        $this->ensureAdminAccess();

        $sort = $_GET['sort'] ?? 'date_creation';
        $direction = $_GET['dir'] ?? 'DESC';

        $articles = $this->articleModel->getArticlesWithStats($sort, $direction);

        $data = [
            'articles' => $articles,
            'currentSort' => $sort,
            'currentDirection' => $direction
        ];

        $this->viewRenderer->render('admin/admin.phtml', $data);
    }

    /**
     * Affichage du formulaire d'ajout ou d'édition d'un article.
     */
    public function displayArticleForm(int $id = null): void
    {
        $this->ensureAdminAccess();

        $article = $id ? $this->articleModel->getArticleById($id) : null;

        $this->viewRenderer->render('admin/article_form.phtml', [
            'article' => $article,
            'title' => $id ? "Modifier l'article" : "Créer un article"
        ]);
    }

    /**
     * Ajoute un article.
     */
    public function addArticle(): void
    {
        $this->ensureAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');

            if (empty($title) || empty($content)) {
                $this->viewRenderer->addFlash('error', "Tous les champs sont obligatoires.");
                header('Location: ' . $this->viewRenderer->url('/admin/addArticle'));
                exit;
            }

            $this->articleModel->addArticle([
                'title' => $title,
                'content' => $content,
                'id_user' => $_SESSION['user']['id'],
                'date_update' => null
            ]);

            $this->viewRenderer->addFlash('success', "Article ajouté avec succès.");
            header('Location: ' . $this->viewRenderer->url('/admin'));
            exit;
        }
    }

    /**
     * Modifie un article existant.
     */
    public function editArticle(int $id): void
    {
        $this->ensureAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');

            if (empty($title) || empty($content)) {
                $this->viewRenderer->addFlash('error', "Tous les champs sont obligatoires.");
                header('Location: ' . $this->viewRenderer->url("/admin/edit/$id"));
                exit;
            }

            $this->articleModel->updateArticle($id, [
                'title' => $title,
                'content' => $content,
                'date_update' => date('Y-m-d H:i:s')
            ]);

            $this->viewRenderer->addFlash('success', "Article modifié avec succès.");
            header('Location: ' . $this->viewRenderer->url('/admin'));
            exit;
        }
    }

    /**
     * Supprime un article.
     */
    public function deleteArticle(int $id): void
    {
        $this->ensureAdminAccess();

        if ($this->articleModel->deleteArticle($id)) {
            $this->viewRenderer->addFlash('success', "Article supprimé avec succès.");
        } else {
            $this->viewRenderer->addFlash('error', "Erreur lors de la suppression de l'article.");
        }

        header('Location: ' . $this->viewRenderer->url('/admin'));
        exit;
    }
}
