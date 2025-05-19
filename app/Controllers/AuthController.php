<?php

namespace App\Controllers;

use App\Models\User;

class AuthController {
    private $userModel;

    public function __construct(User $userModel) {
        $this->userModel = $userModel;
    }

    public function login($email, $password) {
        $user = $this->userModel->getUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        return false;
    }

    public function register($name, $email, $password, $role = 'customer') {
        if ($this->userModel->getUserByEmail($email)) {
            return false;
        }
        return $this->userModel->registerUser($name, $email, $password, $role);
    }
}

