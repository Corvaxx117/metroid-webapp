<?php

namespace App\Model;

use App\Model\BaseModel;

/**
 * Classe qui gère les articles
 */
class ArticleModel extends BaseModel
{
    protected string $table = 'article';

    /**
     * Récupère tous les articles.
     * @return array : un tableau d'objets Article.
     */
    public function getAllArticles(): array
    {
        return parent::findAll();
    }

    /**
     * Récupère un article par son id.
     * @param int $id : l'id de l'article.
     * @return Article|null : un objet Article ou null si l'article n'existe pas.
     */
    public function getArticleById(int $id): ?array
    {
        return parent::findOneBy(['id' => $id]);
    }

    /**
     * Ajoute un article.
     * @param array $data : les données de l'article.
     * @return void
     */
    public function addArticle(array $data): bool
    {
        return $this->create($data);
    }

    /**
     * Modifie les données de l'article.
     * @param int $id L'identifiant de l'article
     * @param array $data Les nouvelles données de l'article
     */
    public function updateArticle(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Supprime un utilisateur par son ID.
     * @param int $id L'identifiant de l'utilisateurà supprimer
     */
    public function deleteArticle(int $id): bool
    {
        return $this->delete(['id' => $id]);
    }

    /**
     * Récupère tous les articles avec statistiques et tri dynamique.
     *
     * @param string $orderBy Colonne de tri (title, views, comments, date_creation)
     * @param string $direction Ordre de tri (ASC ou DESC)
     * @return array
     */
    public function getArticlesWithStats(string $orderBy = 'date_creation', string $direction = 'DESC'): array
    {
        return $this->findAllWithStats($orderBy, $direction, [
            'views' => ['table' => 'article', 'on' => 'a.id = a.id', 'field' => 'a.views'],
            'comments' => ['table' => 'comment c', 'on' => 'c.id_article = a.id', 'field' => 'COUNT(c.id)']
        ]);
    }

    /**
     * Incrémente le nombre de vues d'un article.
     */
    public function incrementArticleViews(int $id): bool
    {
        return $this->incrementViews('id', $id);
    }
}
