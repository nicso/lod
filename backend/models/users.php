<?php

namespace App\models;

use PDO;
use PDOException;

class users
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;

    public function __construct(int $id, string $username, string $password, string $email)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
    }
    
}