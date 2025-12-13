<?php
//Modelo de usuario y manejo básico
require_once __DIR__ . '/Database.php';
class User {
    private $pdo;
    public function __construct(){ $this->pdo = (new Database())->pdo(); }

    public function create($data){
        $stmt = $this->pdo->prepare('INSERT INTO users (name,email,password,role_id) VALUES (:name,:email,:password,:role)');
        $stmt->execute([
            ':name'=>$data['name'],
            ':email'=>$data['email'],
            ':password'=>password_hash($data['password'], PASSWORD_DEFAULT),
            ':role'=>$data['role_id'] ?? null
        ]);
        return $this->pdo->lastInsertId();
    }

    public function findByEmail($email){
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email=:email LIMIT 1');
        $stmt->execute([':email'=>$email]); return $stmt->fetch();
    }

    public function find($id){
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id=:id LIMIT 1');
        $stmt->execute([':id'=>$id]); return $stmt->fetch();
    }
}
?>