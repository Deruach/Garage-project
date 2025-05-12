<?php

namespace App\Controllers;

use App\Models\User;

class AuthController {
    private $userModel;

    public function __construct(User $userModel) {
        $this->userModel = $userModel;
    }

    // Login methode
    public function login($email, $password) {
        $user = $this->userModel->getUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            // Start een sessie en sla gebruikersinformatie op
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            return true;
        }
        return false;
    }

    // Register methode
    public function register($email, $password) {
        // Controleer of de gebruiker al bestaat
        if ($this->userModel->getUserByEmail($email)) {
            return false; // Gebruiker bestaat al
        }
        return $this->userModel->registerUser($email, $password); // Nieuwe gebruiker toevoegen
    }
}
