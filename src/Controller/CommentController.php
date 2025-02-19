<?php

namespace App\Controller;

/**
 * Controleur de la partie commentaire.
 */

use App\Model\ArticleModel;
use App\Model\CommentModel;
use App\Services\ViewRenderer;
use App\Services\FlashMessage;
use App\Services\UrlGenerator;

class CommentController
{
    private CommentModel $commentModel;
    private ArticleModel $articleModel;


    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->commentModel = new CommentModel();
        $this->articleModel = new ArticleModel();
    }
    /**
     * Ajoute un commentaire.
     * @return void
     */
    public function addComment(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Récupération des données du formulaire.
            $pseudo = trim($_POST['pseudo'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $idArticle = $_POST['idArticle'];

            if (empty($pseudo) || empty($content) || empty($idArticle)) {
                FlashMessage::add(FlashMessage::ERROR, "Tous les champs sont obligatoires.");
            }

            // On vérifie que l'article existe.
            $article = $this->articleModel->getArticleById($idArticle);
            if (!$article) {
                FlashMessage::add(FlashMessage::ERROR, "L'article demandé n'existe pas.");
            }
            // On ajoute le commentaire.
            $result = $this->commentModel->addComment([
                'pseudo' => $pseudo,
                'content' => $content,
                'id_article' => $idArticle
            ]);
            // On vérifie que l'ajout a bien fonctionné.
            if (!$result) {
                FlashMessage::add(FlashMessage::ERROR, "Une erreur est survenue lors de l'ajout du commentaire.");
            }
            // Si des erreurs sont rencontrées, on redirige vers la page d'ajout d'article.
            if (FlashMessage::has(FlashMessage::ERROR)) {
                header('Location: ' . UrlGenerator::getUrlFromPath('/articles/add'));
                exit;
            }
            // On redirige vers la page de l'article avec un message de succès.
            FlashMessage::add(FlashMessage::SUCCESS, "Commentaire publié avec succès !");

            header('Location: ' . UrlGenerator::getUrlFromPath('/articles/show/:id', ['id' => $article['id']]));
            exit;
        }
    }
}
