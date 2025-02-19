<?php

namespace App\Model;

use App\Model\BaseModel;

/**
 * Cette classe gère les commentaires. 
 */
class CommentModel extends BaseModel
{
    protected string $table = 'comment';
    /**
     * Récupère tous les commentaires d'un article.
     * @param int $idArticle : l'id de l'article.
     * @return array : un tableau d'objets Comment.
     */
    public function getAllCommentsByArticleId(int $idArticle): array
    {
        return parent::findBy(['id_article' => $idArticle]);
    }

    /**
     * Récupère un commentaire par son id.
     * @param int $id : l'id du commentaire.
     * @return array|null : un objet Comment ou null si le commentaire n'existe pas.
     */
    public function getCommentById(int $id): ?array
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * Ajoute un commentaire.
     * @param array $data : les données du commentaire.
     * @return bool : true si l'ajout a réussi, false sinon.
     */
    public function addComment(array $data): bool
    {
        return $this->create($data);
    }

    /**
     * Supprime un commentaire.
     * @param int $id : l'id du commentaire.
     * @return bool : true si la suppression a réussi, false sinon.
     */
    public function deleteComment(int $id): bool
    {
        return $this->delete(['id' => $id]);
    }
}
