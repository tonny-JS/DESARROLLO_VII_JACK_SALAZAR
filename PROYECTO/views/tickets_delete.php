<?php
require_login();
$uid = userId();

$ticketId = intval($_GET['id'] ?? 0);

if ($ticketId <= 0) {
    echo "<h3>ID de ticket inválido</h3>";
    exit;
}

// Obtener el ticket
$stmt = $db->prepare('SELECT event_id FROM tickets WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $ticketId]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo "<h3>Ticket no encontrado</h3>";
    exit;
}

$eventId = (int)$ticket['event_id'];

// Verificar que el usuario es organizador del evento
$org = $db->prepare(
    'SELECT o.id FROM organizers o
     JOIN events e ON e.organizer_id = o.id
     WHERE e.id = :eid AND o.user_id = :uid
     LIMIT 1'
);
$org->execute([':eid' => $eventId, ':uid' => $uid]);
$orgRow = $org->fetch(PDO::FETCH_ASSOC);

if (!$orgRow) {
    echo "<h3>No tienes permiso para eliminar este ticket</h3>";
    exit;
}

// Eliminar ticket
$del = $db->prepare('DELETE FROM tickets WHERE id = :id');
$del->execute([':id' => $ticketId]);

// Volver a la lista de tickets del evento
header("Location: " . BASE_URL . "/index.php?view=tickets&event_id=$eventId");
exit;
