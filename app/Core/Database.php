<?php

namespace app\Core;

use PDO;
use PDOException;

class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $pdo;

    public function __construct($config) {
        $this->host = $config['host'];
        $this->dbname = $config['dbname'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        
        $this->connect();
    }

    private function connect() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname}";
        $tries = 5;

        while ($tries > 0) {
            try {
                $this->pdo = new PDO($dsn, $this->username, $this->password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return;  // succesvol verbonden, exit connect()
            } catch (PDOException $e) {
                $tries--;
                if ($tries === 0) {
                    die("Connection failed after multiple attempts: " . $e->getMessage());
                }
                sleep(2);  // wacht 2 seconden voordat opnieuw proberen
            }
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
