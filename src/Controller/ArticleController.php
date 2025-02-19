<?php

namespace App\Controller;

use App\Model\ArticleModel;
use App\Model\CommentModel;
use App\Services\ViewRenderer;
use App\Services\FlashMessage;
use App\Services\UrlGenerator;
use App\Services\Utils;

/**
 * Contrôleur de la partie article
 */
class ArticleController
{
    private ArticleModel $articleModel;
    private CommentModel $commentModel;

    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->articleModel = new ArticleModel();
        $this->commentModel = new CommentModel();
    }
    /**
     * Affiche la page d'accueil.
     * @return void
     */
    public function list(): void
    {
        $articles = $this->articleModel->getAllArticles();
        $data = [
            'title' => 'Liste des articles',
            'articles' => $articles
        ];
        $this->viewRenderer->render('../views/articles/list.phtml', $data);
    }

    /**
     * Affiche le détail d'un article.
     * @return void
     */
    public function show(int $id): void
    {
        // Récupération de l'id de l'article demandé.
        $article = $this->articleModel->getArticleById($id);
        if (!$article) {
            FlashMessage::add(FlashMessage::ERROR, "L'article demandé n'existe pas.");
            header('Location: ' . UrlGenerator::getUrlFromPath('/articles'));
            exit;
        }

        $comments = $this->commentModel->getAllCommentsByArticleId($id);

        $data = [
            'title' => 'Détails de l\'article',
            'article' => $article,
            'comments' => $comments
        ];
        $this->viewRenderer->render('../views/articles/show.phtml', $data);
    }

    /**
     * Affiche le formulaire d'ajout d'un article.
     * @return void
     */
    public function addArticle(): void {}

    /**
     * Affiche la page "à propos".
     * @return void
     */
    public function showApropos(): void
    {
        $this->viewRenderer->render("../views/a-propos.phtml");
    }
}
