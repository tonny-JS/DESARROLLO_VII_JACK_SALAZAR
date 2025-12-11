<?php
// src/Ticket.php - Modelo de tickets/entradas por evento
require_once __DIR__ . '/Database.php';

class Ticket {
  private $pdo;

  public function __construct() {
    $this->pdo = (new Database())->pdo();
  }

  public function create($data) {
    try {
      $stmt = $this->pdo->prepare(
        'INSERT INTO tickets (event_id, name, price, quantity) 
         VALUES (:event_id, :name, :price, :quantity)'
      );
      $stmt->execute([
        ':event_id' => intval($data['event_id']),
        ':name'     => trim($data['name']),
        ':price'    => floatval($data['price']),
        ':quantity' => intval($data['quantity'])
      ]);
      return $this->pdo->lastInsertId();
    } catch (Exception $e) {
      throw new Exception("Error creando ticket: " . $e->getMessage());
    }
  }

  public function forEvent($event_id) {
    $stmt = $this->pdo->prepare('SELECT * FROM tickets WHERE event_id=:eid ORDER BY id');
    $stmt->execute([':eid' => intval($event_id)]);
    return $stmt->fetchAll();
  }

  public function find($id) {
    $stmt = $this->pdo->prepare('SELECT * FROM tickets WHERE id=:id LIMIT 1');
    $stmt->execute([':id' => intval($id)]);
    return $stmt->fetch();
  }

  public function update($id, $data) {
    $stmt = $this->pdo->prepare(
      'UPDATE tickets SET name=:name, price=:price, quantity=:quantity WHERE id=:id'
    );
    $stmt->execute([
      ':id'       => intval($id),
      ':name'     => trim($data['name']),
      ':price'    => floatval($data['price']),
      ':quantity' => intval($data['quantity'])
    ]);
  }

  public function delete($id) {
    $stmt = $this->pdo->prepare('DELETE FROM tickets WHERE id=:id');
    $stmt->execute([':id' => intval($id)]);
  }

  public function remaining($ticket_id) {
    $stmt = $this->pdo->prepare('SELECT quantity FROM tickets WHERE id=:id');
    $stmt->execute([':id' => intval($ticket_id)]);
    $t = $stmt->fetch();
    if (!$t) return 0;

    $stmt2 = $this->pdo->prepare("
      SELECT COALESCE(SUM(quantity),0) AS reserved
      FROM registrations
      WHERE ticket_id=:tid AND status IN ('pending','confirmed')
    ");
    $stmt2->execute([':tid' => intval($ticket_id)]);
    $res = $stmt2->fetch();

    return max(0, intval($t['quantity']) - intval($res['reserved']));
  }
}
?>