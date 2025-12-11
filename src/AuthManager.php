<?php
// src/AuthManager.php - login/register helpers used by index.php
require_once __DIR__ . '/User.php';
class AuthManager {
    private $userModel;
    public function __construct(){ $this->userModel = new User(); }

    public function login($email,$password){
        $u = $this->userModel->findByEmail($email);
        if ($u && password_verify($password,$u['password'])) return $u;
        return false;
    }

    public function register($data){
        // $data: name,email,password,role_id
        return $this->userModel->create($data);
    }
}
?>