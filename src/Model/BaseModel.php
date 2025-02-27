<?php

namespace App\Model;

use App\Core\Database;
use PDO;

abstract class BaseModel
{
    protected PDO $pdo;
    protected string $table;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Récupérer tous les enregistrements.
     * Exemple : SELECT * FROM table
     * @return array
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->getTableName()}");
        return $stmt->fetchAll();
    }

    /**
     * Trouver un enregistrement unique selon des critères.
     * Exemple : SELECT * FROM table WHERE column = value LIMIT 1
     * @return array|null
     */
    public function findOneBy(array $criteria): ?array
    {
        $conditions = $this->buildConditions($criteria);
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$conditions} LIMIT 1");
        $stmt->execute($criteria);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Trouver plusieurs enregistrements selon des critères.
     * Exemple : SELECT * FROM table WHERE column = value
     * @return array
     */
    public function findBy(array $criteria): array
    {
        $conditions = $this->buildConditions($criteria);
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$conditions}");
        $stmt->execute($criteria);
        return $stmt->fetchAll();
    }

    /**
     * Crée une nouvelle entrée dans la table.
     * Exemple : INSERT INTO table (column1, column2) VALUES (:column1, :column2)
     */
    public function create(array $data): bool
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_map(fn($key) => ":$key", array_keys($data)));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Met à jour un enregistrement en fonction de son ID.
     * Exemple : UPDATE table SET column1 = value1, column2 = value2 WHERE id = :id
     */
    public function update(int $id, array $criteria): bool
    {
        // Ajout de l'ID aux paramètres
        $criteria['id'] = $id;
        $setClause = $this->buildSetClause($criteria);
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($criteria);
    }

    /**
     * Supprime des entrées selon des critères donnés.
     * Exemple : DELETE FROM table WHERE column = value
     */
    public function delete(array $criteria): bool
    {
        $conditions = $this->buildConditions($criteria);
        $sql = "DELETE FROM {$this->table} WHERE {$conditions}";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($criteria);
    }

    /**
     * Fonction utilitaire pour construire les conditions SQL.
     * 
     * @param array $criteria Tableau associatif clé => valeur
     * @return string Clause WHERE SQL générée (ex: email = :email AND lastname = :lastname)
     */
    private function buildConditions(array $criteria): string
    {
        // array_map applique une fonction à chaque élément du tableau
        return implode(' AND ', array_map(fn($key) => "$key = :$key", array_keys($criteria)));
        // resultat : email = :email AND lastname = :lastname
    }

    /**
     * Fonction utilitaire pour construire la clause SET pour une requête UPDATE.
     * 
     * @param array $data Tableau associatif clé => valeur
     * @return string Clause SET SQL générée (ex: "column1 = :column1, column2 = :column2")
     */
    private function buildSetClause(array $data): string
    {
        return implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
    }

    /**
     * Chaque modèle enfant doit définir le nom de la table associée.
     * @return string Nom de la table
     */
    protected function getTableName(): string
    {
        return $this->table;
    }

    /**
     * Incremente le nombre de vues d'un enregistrement.
     * 
     * @param int $id Identifiant de l'enregistrement
     * @return bool true si la mise à jour a fonctionné, false sinon
     */
    public function incrementViews(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    /**
     * Récupérer des enregistrements avec statistiques et tri dynamique.
     *
     * @param string $orderBy Colonne de tri (par défaut: 'date_creation')
     * @param string $direction Ordre de tri ('ASC' ou 'DESC')
     * @param array $stats Colonnes de statistiques supplémentaires (ex: ['views' => ['table' => 'article', 'on' => 'a.id = a.id', 'field' => 'a.views']])
     * @return array Résultats triés avec statistiques
     */
    public function findAllWithStats(string $orderBy = 'date_creation', string $direction = 'DESC', array $stats = []): array
    {
        /**
         * Vérification de la colonne de tri
         * 
         * - `allowedColumns` contient les colonnes autorisées pour le tri :
         *   → `['id', 'title', 'date_creation']` : Colonnes classiques de la table.
         *   → `array_keys($stats)` : Colonnes statistiques (ex: `views`, `comments`).
         * - Si `$orderBy` n'est pas dans cette liste, on force `date_creation`.
         */
        $allowedColumns = array_merge(['id', 'title', 'date_creation'], array_keys($stats));
        if (!in_array($orderBy, $allowedColumns)) {
            $orderBy = 'date_creation'; // Sécurité pour éviter les injections SQL
        }

        /**
         * Vérification et sécurisation de l'ordre de tri
         * 
         * - On s'assure que `$direction` soit soit `ASC` soit `DESC`.
         * - Toute autre valeur sera forcée en `DESC`.
         */
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        /**
         * Initialisation des parties de la requête SQL
         * 
         * - `$joinClauses` : Liste des `LEFT JOIN` pour récupérer les stats.
         * - `$selectFields` : Champs à sélectionner dans le `SELECT`.
         */
        $joinClauses = [];  // Stocke les `LEFT JOIN`
        $selectFields = ["a.*"]; // On sélectionne toutes les colonnes de `article` (alias `a`)

        /**
         *  Génération dynamique des statistiques à inclure dans la requête
         * 
         * - `$stats` est un tableau associatif où :
         *   → Clé = Alias du champ (ex: `views`, `comments`)
         *   → Valeur = Tableau contenant :
         *     → `'table'` : Table concernée
         *     → `'on'` : Condition du `JOIN`
         *     → `'field'` : Calcul ou colonne à sélectionner
         * 
         *  Exemple de `$stats` en entrée :
         * ```php
         * [
         *    'views' => ['table' => 'article', 'on' => 'a.id = article.id', 'field' => 'a.views'],
         *    'comments' => ['table' => 'comment c', 'on' => 'c.id_article = a.id', 'field' => 'COUNT(c.id)']
         * ]
         * ```
         */
        foreach ($stats as $alias => $stat) {
            // Ajouter un `LEFT JOIN` pour la statistique concernée
            $joinClauses[] = "LEFT JOIN {$stat['table']} ON {$stat['on']}";
            // Ajouter la statistique au `SELECT`
            $selectFields[] = "{$stat['field']} AS {$alias}";
        }

        /**
         * Construction de la requête SQL finale
         * 
         * - `SELECT` : Sélectionne tous les champs de `article` + les stats
         * - `FROM` : Utilise `article` comme table principale
         * - `LEFT JOIN` : Ajoute dynamiquement les stats via des `LEFT JOIN`
         * - `GROUP BY a.id` : Regroupe les résultats par article pour éviter les doublons
         * - `ORDER BY` : Trie les résultats selon `$orderBy` et `$direction`
         */
        /**
         * Exemple ici 
         * 
         * SELECT a.*, a.views AS views, COUNT(c.id) AS comments
         * FROM article a
         * LEFT JOIN article ON a.id = article.id
         * LEFT JOIN comment c ON c.id_article = a.id
         * GROUP BY a.id
         * ORDER BY views DESC
         *
         * Sortie : 
         * 
         * [
         *   [
         *       'id' => 1,
         *       'title' => 'Mon premier article',
         *       'date_creation' => '2024-02-01',
         *       'views' => 150,
         *       'comments' => 12
         *   ],
         *  [
         *      'id' => 2,
         *      'title' => 'Deuxième article',
         *      'date_creation' => '2024-01-15',
         *       'views' => 98,
         *       'comments' => 8
         *   ]
         * ]
         */
        $sql = "
            SELECT " . implode(", ", $selectFields) . "
            FROM {$this->getTableName()} a
            " . implode(" ", $joinClauses) . "
            GROUP BY a.id
            ORDER BY {$orderBy} {$direction}
        ";

        // Exécution de la requête et récupération des résultats
        return $this->pdo->query($sql)->fetchAll();
    }
}
