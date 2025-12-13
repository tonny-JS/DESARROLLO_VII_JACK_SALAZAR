<?php
// src/Access.php - Control de accesos (marcar entrada al evento)
require_once __DIR__ . '/Database.php';

class Access {
  private $pdo;

  public function __construct() {
    $this->pdo = (new Database())->pdo();
  }

  public function log($registration_id) {
    $stmt = $this->pdo->prepare('INSERT INTO accesses (registration_id) VALUES (:rid)');
    $stmt->execute([':rid' => intval($registration_id)]);
    return $this->pdo->lastInsertId();
  }
}
?>