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
     * Trouver un enregistrement unique selon des critères.
     * Exemple : SELECT * FROM table WHERE column = value LIMIT 1
     */
    public function findOneBy(array $criteria): ?array
    {
        $conditions = $this->buildConditions($criteria);
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE {$conditions} LIMIT 1");
        $stmt->execute($criteria);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Récupérer tous les enregistrements.
     * Exemple : SELECT * FROM table
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->getTableName()}");
        return $stmt->fetchAll();
    }

    /**
     * Trouver plusieurs enregistrements selon des critères.
     * Exemple : SELECT * FROM table WHERE column = value
     */
    public function findBy(array $criteria): array
    {
        $conditions = $this->buildConditions($criteria);
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE {$conditions}");
        $stmt->execute($criteria);
        return $stmt->fetchAll();
    }

    /**
     * Crée une nouvelle entrée dans la table.
     */
    public function create(array $data): bool
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_map(fn($key) => ":$key", array_keys($data)));

        $sql = "INSERT INTO {$this->getTableName()} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Supprime des entrées selon des critères donnés.
     * Exemple : DELETE FROM table WHERE column = value
     */
    public function delete(array $criteria): bool
    {
        $conditions = $this->buildConditions($criteria);
        $sql = "DELETE FROM {$this->getTableName()} WHERE {$conditions}";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($criteria);
    }

    /**
     * Fonction utilitaire pour construire les conditions SQL.
     */
    // $criteria est un tableau associatif contenant les colonnes et leurs valeurs à rechercher.
    // ex: $criteria = ['email' => 'toto@toto.fr', 'lastname' => 'Dupont']
    private function buildConditions(array $criteria): string
    {
        // array_map applique une fonction à chaque élément du tableau
        // Pour chaque clé (ex: email), retourne une chaîne de type email = :email
        return implode(' AND ', array_map(fn($key) => "$key = :$key", array_keys($criteria)));
        // resultat : email = :email AND lastname = :lastname
    }

    /**
     * Chaque modèle enfant doit définir le nom de la table associée.
     */
    abstract protected function getTableName(): string;
}
