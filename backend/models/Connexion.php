<?php
namespace App\Models;

use PDO;
use PDOException;

class Connexion {
    private static ?Connexion $instance = null;
    private ?PDO $conn = null;

    private function __construct() {
        try {
            $this->conn = new PDO(
                'mysql:host=mariadb;dbname=lod',
                'root',
                'pass',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false // Ajouté pour avoir de meilleurs messages d'erreur
                ]
            );
            $this->conn->exec("SET CHARACTER SET utf8");
            error_log("Connexion PDO établie avec succès");
        } catch (PDOException $e) {
            error_log("Erreur PDO dans le constructeur: " . $e->getMessage());
            throw new PDOException($e->getMessage());
        }
    }

    public static function getInstance(): Connexion {
        if (self::$instance === null) {
            self::$instance = new Connexion();
        }
        return self::$instance;
    }

    public function getConn(): PDO {
        if ($this->conn === null) {
            throw new PDOException("La connexion n'est pas établie");
        }
        return $this->conn;
    }

    // Empêcher le clonage de l'instance
    private function __clone() {}

    // Empêcher la désérialisation de l'instance
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
