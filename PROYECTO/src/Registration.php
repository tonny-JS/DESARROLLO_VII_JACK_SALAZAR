<?php
// src/Registration.php - Modelo de inscripciones (compras) y gestión de capacidad.
require_once __DIR__ . '/Database.php';

class Registration {
  private $pdo;

  public function __construct() {
    $this->pdo = (new Database())->pdo();
  }

  public function eventCapacity($event_id) {
    $stmt = $this->pdo->prepare("
      SELECT CASE WHEN e.capacity IS NULL THEN v.capacity ELSE e.capacity END AS capacity
      FROM events e
      LEFT JOIN venues v ON v.id = e.venue_id
      WHERE e.id=:id LIMIT 1
    ");
    $stmt->execute([':id' => intval($event_id)]);
    $row = $stmt->fetch();
    return intval($row['capacity'] ?? 0);
  }

  public function eventReserved($event_id) {
    $stmt = $this->pdo->prepare("
      SELECT COALESCE(SUM(quantity),0) AS reserved
      FROM registrations
      WHERE event_id=:id AND status IN ('pending','confirmed')
    ");
    $stmt->execute([':id' => intval($event_id)]);
    $row = $stmt->fetch();
    return intval($row['reserved']);
  }

  public function ticketRemaining($ticket_id) {
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

  public function create($data) {
    $event_id  = intval($data['event_id']);
    $ticket_id = intval($data['ticket_id']);
    $qty       = max(1, intval($data['quantity']));

    $cap      = $this->eventCapacity($event_id);
    $reserved = $this->eventReserved($event_id);

    if ($cap <= 0) {
      throw new Exception('El evento no tiene capacidad configurada.');
    }
    if ($reserved + $qty > $cap) {
      throw new Exception('Capacidad insuficiente en el evento.');
    }

    $remainingTicket = $this->ticketRemaining($ticket_id);
    if ($remainingTicket < $qty) {
      throw new Exception('No hay suficientes entradas de este tipo.');
    }

    $stmtPrice = $this->pdo->prepare('SELECT price FROM tickets WHERE id=:id');
    $stmtPrice->execute([':id' => $ticket_id]);
    $t = $stmtPrice->fetch();
    if (!$t) {
      throw new Exception('El ticket no existe.');
    }
    $total = floatval($t['price']) * $qty;

    $this->pdo->beginTransaction();
    try {
      $stmt = $this->pdo->prepare("
        INSERT INTO registrations (user_id, event_id, ticket_id, quantity, total_price, status)
        VALUES (:user_id, :event_id, :ticket_id, :quantity, :total_price, 'pending')
      ");
      $stmt->execute([
        ':user_id'    => $data['user_id'],
        ':event_id'   => $event_id,
        ':ticket_id'  => $ticket_id,
        ':quantity'   => $qty,
        ':total_price'=> $total
      ]);

      $id = $this->pdo->lastInsertId();
      $this->pdo->commit();
      return $id;
    } catch (Exception $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }

  public function updateStatus($id, $newStatus) {
    $allowed = ['pending','confirmed','cancelled','refunded'];
    if (!in_array($newStatus, $allowed)) return;

    $stmt = $this->pdo->prepare('UPDATE registrations SET status=:s WHERE id=:id');
    $stmt->execute([':s' => $newStatus, ':id' => intval($id)]);
  }

  public function find($id) {
    $stmt = $this->pdo->prepare('SELECT * FROM registrations WHERE id=:id LIMIT 1');
    $stmt->execute([':id' => intval($id)]);
    return $stmt->fetch();
  }

  public function forUser($user_id) {
    $stmt = $this->pdo->prepare('SELECT * FROM registrations WHERE user_id=:uid ORDER BY created_at DESC');
    $stmt->execute([':uid' => intval($user_id)]);
    return $stmt->fetchAll();
  }

  public function organizerStats($organizer_id) {
    $stmtE = $this->pdo->prepare('SELECT id, title FROM events WHERE organizer_id=:oid');
    $stmtE->execute([':oid' => intval($organizer_id)]);
    $events = $stmtE->fetchAll();

    $stats = [
      'events_total' => count($events),
      'registrations_total' => 0,
      'confirmed_total' => 0,
      'revenue_confirmed' => 0.0,
      'per_event' => []
    ];

    foreach ($events as $ev) {
      $evId = $ev['id'];

      $stmtR = $this->pdo->prepare('SELECT COUNT(*) AS c FROM registrations WHERE event_id=:id');
      $stmtR->execute([':id' => $evId]);
      $c = $stmtR->fetch()['c'];

      $stmtC = $this->pdo->prepare("
        SELECT COALESCE(SUM(quantity),0) AS qty, COALESCE(SUM(total_price),0) AS revenue
        FROM registrations WHERE event_id=:id AND status='confirmed'
      ");
      $stmtC->execute([':id' => $evId]);
      $rc = $stmtC->fetch();

      $stats['registrations_total'] += intval($c);
      $stats['confirmed_total']     += intval($rc['qty']);
      $stats['revenue_confirmed']   += floatval($rc['revenue']);

      $stats['per_event'][] = [
        'event_id' => $evId,
        'title'    => $ev['title'],
        'regs'     => intval($c),
        'confirmed'=> intval($rc['qty']),
        'capacity' => $this->eventCapacity($evId),
        'reserved' => $this->eventReserved($evId),
        'occupancy_percent' => $cap > 0 ? round(($this->eventReserved($evId) / $cap) * 100, 2) : 0
      ];
    }
    return $stats;
  }
}
?>