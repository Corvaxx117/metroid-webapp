<?php

namespace Mini\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    // Empêche l'instanciation directe depuis l'extérieur
    private function __construct() {}

    // Empêche la duplication de l'instance via clone
    private function __clone() {}

    // Empêche la désérialisation via unserialize()
    public function __wakeup() {}

    /**
     * Retourne l'instance unique de PDO (Singleton).
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                // Charger les variables depuis .env
                $dsn = $_ENV['DB_DSN'];         // "mysql:host=localhost;dbname=mydb;charset=utf8"
                $username = $_ENV['DB_USER'];
                $password = $_ENV['DB_PASS'];

                // Création de l'instance PDO avec des options
                self::$instance = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT         => true,  // Connexion persistante
                ]);
            } catch (PDOException $e) {
                die('Erreur de connexion à la base de données : ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
