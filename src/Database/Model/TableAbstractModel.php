<?php

namespace Metroid\Database\Model;

use Metroid\Database\Connection;

abstract class TableAbstractModel
{
    protected string $table;
    protected \PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    /**
     * Return the PDO instance of the connection.
     * @return \PDO instance of PDO class.
     */
    protected function getPdo(): \PDO
    {
        return $this->connection;
    }

    /**
     * Trouver un enregistrement unique selon des critères.
     * Exemple : SELECT * FROM table WHERE column = value LIMIT 1
     * @return array|null
     */
    public function findOneBy(array $criteria): ?array
    {
        $conditions = $this->buildConditions($criteria);
        $stmt = $this->getPdo()->prepare("SELECT * FROM {$this->table} WHERE {$conditions} LIMIT 1");
        $stmt->execute($criteria);
        $result = $stmt->fetch();

        return $result ?: null;
    }


    /**
     * Récupère plusieurs enregistrements selon des critères.
     * 
     * @param array $criteria Tableau associatif clé => valeur des critères de recherche (ex: ['books.id' => 1, 'books.title' => 'Foo'])
     * @param string $joinClause Clause JOIN SQL (ex: "JOIN books ON books.id = authors.id")
     * @param string $select Clause SELECT SQL (ex: "books.*")
     * @param string|null $orderBy Colonne de tri (par défaut: null)
     * @param int|null $limit Nombre d'enregistrements maximum (par défaut: null)
     * @param int|null $offset Décalage du premier enregistrement (par défaut: null)
     * @return array Résultats de la requête
     */

    public function findBy(
        array $criteria = [],
        string $joinClause = '',
        string $select = '*',
        ?string $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $conditions = '';
        $params = [];

        if (!empty($criteria)) {
            $parts = [];

            foreach ($criteria as $column => $value) {
                // Convertit books.id => books_id (clé PDO valide)
                $paramName = str_replace('.', '_', $column);
                $parts[] = "$column = :$paramName";
                $params[$paramName] = $value;
            }

            $conditions = 'WHERE ' . implode(' AND ', $parts);
        }

        $sql = "SELECT $select FROM {$this->table} $joinClause $conditions";

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        if ($limit !== null) {
            $sql .= " LIMIT $limit";
            if ($offset !== null) {
                $sql .= " OFFSET $offset";
            }
        }

        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute($params);

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
        $stmt = $this->getPdo()->prepare($sql);
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
        $stmt = $this->getPdo()->prepare($sql);
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
        $stmt = $this->getPdo()->prepare($sql);
        return $stmt->execute($criteria);
    }

    /**
     * Fonction utilitaire pour construire les conditions SQL.
     *
     * @param array $criteria Tableau associatif clé => valeur
     * @return string Clause WHERE SQL générée (ex: email = :email AND lastname = :lastname)
     */
    protected function buildConditions(array $criteria): string
    {
        $conditions = [];

        foreach ($criteria as $column => $value) {
            $param = str_replace('.', '_', $column); // books.id => books_id
            $conditions[] = "$column = :$param";
        }

        return implode(' AND ', $conditions);
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
}
