<?php
// views/registration_form.php

// Cargar evento y tickets
$eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
if ($eventId <= 0) {
    header('Location: ' . BASE_URL . '/index.php?view=events');
    exit;
}

$stmt = $db->prepare('SELECT e.*, o.user_id AS organizer_user_id FROM events e JOIN organizers o ON e.organizer_id = o.id WHERE e.id = :id AND e.status = "published" LIMIT 1');
$stmt->execute([':id' => $eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

$tickets = [];
if ($event) {
    $tstmt = $db->prepare('
        SELECT t.id, t.name, t.price, t.quantity AS issued,
               COALESCE((SELECT SUM(r.quantity) FROM registrations r WHERE r.ticket_id=t.id AND r.status IN ("pending","confirmed")),0) AS reserved
        FROM tickets t
        WHERE t.event_id = :eid
        ORDER BY t.id
    ');
    $tstmt->execute([':eid' => $eventId]);
    $tickets = $tstmt->fetchAll(PDO::FETCH_ASSOC);
}

// ¿Usuario logueado?
$currentUserId = userId();

// ¿Es el usuario organizador del evento?
$isOrganizerOfEvent = false;
if ($event && $currentUserId) {
    $q = $db->prepare('SELECT 1 FROM organizers o WHERE o.id = :oid AND o.user_id = :uid LIMIT 1');
    $q->execute([':oid' => $event['organizer_id'], ':uid' => $currentUserId]);
    $isOrganizerOfEvent = (bool)$q->fetchColumn();
}

// Si se envió POST -> procesar reserva/compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // validar CSRF
    csrf_check($_POST['csrf'] ?? null);

    // bloquear si organizador del evento
    if ($isOrganizerOfEvent) {
        $error = 'Los organizadores no pueden inscribirse a sus propios eventos.';
    } else {
        $ticket_id = (int)($_POST['ticket_id'] ?? 0);
        $quantity  = max(1, (int)($_POST['quantity'] ?? 1));

        if (!$event) {
            $error = 'Evento no disponible';
        } else {
            // consultar ticket elegido
            $tq = $db->prepare('
                SELECT id, price, quantity AS issued,
                       COALESCE((SELECT SUM(r.quantity) FROM registrations r WHERE r.ticket_id=t.id AND r.status IN ("pending","confirmed")),0) AS reserved
                FROM tickets t
                WHERE t.id = :tid
                LIMIT 1
            ');
            $tq->execute([':tid' => $ticket_id]);
            $t = $tq->fetch(PDO::FETCH_ASSOC);

            if (!$t) {
                $error = 'Ticket inválido';
            } else {
                $available = max(0, (int)$t['issued'] - (int)$t['reserved']);
                if ($quantity > $available) {
                    $error = 'No hay suficientes boletos disponibles';
                } else {
                    // calcular total
                    $total = $quantity * (float)$t['price'];

                    // insertar registro (user_id puede ser NULL si no autenticado)
                    $ins = $db->prepare('
                        INSERT INTO registrations (user_id, event_id, ticket_id, quantity, total_price, status, created_at)
                        VALUES (:u, :e, :t, :q, :total, :st, NOW())
                    ');
                    $ins->execute([
                        ':u'     => $currentUserId ? $currentUserId : null,
                        ':e'     => $eventId,
                        ':t'     => $ticket_id,
                        ':q'     => $quantity,
                        ':total' => $total,
                        ':st'    => 'pending'
                    ]);

                    header('Location: ' . BASE_URL . '/index.php?view=my_registrations');
                    exit;
                }
            }
        }
    }
}

// Renderizar formulario (GET o si hubo $error)
ob_start();
?>
<h2>Inscribirme al evento #<?= intval($event['id'] ?? $eventId) ?> — <?= e($event['title'] ?? '') ?></h2>

<?php if (!$event): ?>
  <p class="danger">Evento no encontrado o no publicado.</p>
  <p><a href="<?= e(BASE_URL) ?>/index.php?view=events">⬅️ Volver</a></p>
<?php else: ?>

  <?php if (!empty($error)): ?>
    <p style="color:#b00"><?= e($error) ?></p>
  <?php endif; ?>

  <?php if ($isOrganizerOfEvent): ?>
    <p>No puedes inscribirte a un evento que organizas.</p>
    <p><a href="<?= e(BASE_URL) ?>/index.php?view=events">⬅️ Volver</a></p>
  <?php else: ?>

    <?php if (empty($tickets)): ?>
      <p>No hay tickets disponibles para este evento.</p>
      <p><a href="<?= e(BASE_URL) ?>/index.php?view=events">⬅️ Volver</a></p>
    <?php else: ?>

      <form action="<?= e(BASE_URL) ?>/index.php?view=registration_form&event_id=<?= intval($eventId) ?>" method="post">
        <input type="hidden" name="csrf" value="<?= e(generate_csrf()) ?>">

        <label for="ticket_id">Tipo de entrada:</label>
        <select id="ticket_id" name="ticket_id" required>
          <option value="">— Selecciona —</option>
          <?php foreach ($tickets as $t): 
            $avail = max(0, (int)$t['issued'] - (int)$t['reserved']);
          ?>
            <option value="<?= intval($t['id']) ?>">
              <?= e($t['name']) ?> (<?= $avail ?> disponibles) — $<?= e(number_format((float)$t['price'],2)) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label for="quantity">Cantidad:</label>
        <input id="quantity" type="number" name="quantity" min="1" value="1" required>

        <button type="submit">Comprar / Reservar</button>
      </form>

      <p><a href="<?= e(BASE_URL) ?>/index.php?view=events">⬅️ Volver</a></p>

    <?php endif; // tickets exist ?>

  <?php endif; // is organizer ?>

<?php endif; // event exists ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
