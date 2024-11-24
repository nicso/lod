<?php

namespace models;

use PDO;
use PDOException;

class Connexion
{
//    const SERVER_NAME = "docker-lamp-mariadb-1";
    const SERVER_NAME = "mariadb";
    const USERNAME = "root";
    const PASSWORD = "pass";
    const DB_NAME = 'lod';

    private static $instance = NULL;

    private ?PDO $conn = null;

    static public function getInstance(): ?Connexion
    {
        if (self::$instance === NULL) {
            try {
                self::$instance = new Connexion();
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        return self::$instance;
    }

    /*
     * Protected CTOR
     */
    protected function __construct()
    {
        $this->conn = new PDO("mysql:host=". self::SERVER_NAME .";dbname=".self::DB_NAME, self::USERNAME, self::PASSWORD);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function getConn(): PDO
    {
        return $this->conn;
    }
}
