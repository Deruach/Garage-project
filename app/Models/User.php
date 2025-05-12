<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    // Haal gebruiker op via e-mail
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Voeg nieuwe gebruiker toe
    public function registerUser($email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
