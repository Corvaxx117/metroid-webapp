<?php

namespace Mini\Database\Model;

use Mini\Database\Connection;

abstract class TableAbstractModel
{
    protected string $table;

    public function __construct(protected Connection $connection) {}

    /**
     * Récupérer tous les enregistrements.
     * Exemple : SELECT * FROM table
     * @return array
     */
    public function findAll(): array
    {
        $stmt = $this->connection->getInstance()->query("SELECT * FROM {$this->getTableName()}");
        return $stmt->fetchAll();
    }

    /**
     * Trouver un enregistrement unique selon des critères.
     * Exemple : SELECT * FROM table WHERE column = value LIMIT 1
     * @return array|null
     */
    public function  findOneBy(array $criteria): ?array
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
}
