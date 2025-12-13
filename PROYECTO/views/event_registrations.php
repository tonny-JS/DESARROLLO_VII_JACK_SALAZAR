<?php
require_login();
$uid = userId();

// Validar que es organizador
if (!is_organizer($db, $uid)) {
    header('Location: ' . BASE_URL);
    exit;
}

$eventId = intval($_GET['event_id'] ?? 0);

// Verificar que el evento pertenece a este organizador
$stmt = $db->prepare(
    'SELECT id, title FROM events 
     WHERE id = :id 
     AND organizer_id IN (SELECT id FROM organizers WHERE user_id = :uid)
     LIMIT 1'
);
$stmt->execute([':id' => $eventId, ':uid' => $uid]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Evento no encontrado o no te pertenece.");
}

// Procesar confirmación de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_id'])) {

    csrf_check($_POST['csrf'] ?? '');

    $regId = intval($_POST['reg_id']);

    // Confirmar ticket
    $confirm = $db->prepare(
        'UPDATE registrations 
         SET status = "confirmed" 
         WHERE id = :rid'
    );
    $confirm->execute([':rid' => $regId]);

    header("Location: " . BASE_URL . "/index.php?view=event_registrations&event_id=$eventId");
    exit;
}

// Obtener inscripciones
$stmt = $db->prepare(
    'SELECT r.id, r.quantity, r.total_price, r.status,
            u.name AS username,
            t.name AS ticket_name
     FROM registrations r
     LEFT JOIN users u ON u.id = r.user_id
     JOIN tickets t ON t.id = r.ticket_id
     WHERE r.event_id = :eid
     ORDER BY r.created_at DESC'
);
$stmt->execute([':eid' => $eventId]);
$regs = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h2>Inscripciones del evento: <?= htmlspecialchars($event['title']) ?></h2>

<?php if (empty($regs)): ?>
    <p>No hay inscripciones.</p>

<?php else: ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Usuario</th>
            <th>Ticket</th>
            <th>Cantidad</th>
            <th>Precio total</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>

        <?php foreach ($regs as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['username'] ?: 'Invitado') ?></td>
                <td><?= htmlspecialchars($r['ticket_name']) ?></td>
                <td><?= $r['quantity'] ?></td>
                <td>$<?= number_format($r['total_price'], 2) ?></td>
                <td><?= strtoupper($r['status']) ?></td>

                <td>
                    <?php if ($r['status'] === 'pending'): ?>

                        <form method="post">
                            <input type="hidden" name="csrf" value="<?= generate_csrf() ?>">
                            <input type="hidden" name="reg_id" value="<?= $r['id'] ?>">
                            <button type="submit">Confirmar</button>
                        </form>

                    <?php else: ?>
                        ✔ Confirmado
                    <?php endif; ?>
                </td>

            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<p><a href="<?= BASE_URL ?>/index.php?view=organizer_dashboard">⬅ Volver al panel</a></p>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
