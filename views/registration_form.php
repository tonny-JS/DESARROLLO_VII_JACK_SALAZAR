<?php
// Validar CSRF
csrf_check($_POST['csrf'] ?? null);

$ticket_id = (int)($_POST['ticket_id'] ?? 0);
$quantity  = max(1, (int)($_POST['quantity'] ?? 1));

if (!$event) {
    $error = 'Evento no disponible';
} else {
    // Consultar ticket
    $tq = $db->prepare(
        'SELECT id, price, issued, 
                COALESCE(
                    (SELECT SUM(quantity) 
                     FROM registrations r 
                     WHERE r.ticket_id = t.id 
                       AND r.status IN ("pending","confirmed")
                    ), 0
                ) AS reserved 
         FROM tickets t 
         WHERE t.id = :tid 
         LIMIT 1'
    );
    $tq->execute([':tid' => $ticket_id]);
    $t = $tq->fetch(PDO::FETCH_ASSOC);

    if (!$t) {
        $error = 'Ticket inválido';
    } else {
        $available = max(0, (int)$t['issued'] - (int)$t['reserved']);

        if ($quantity > $available) {
            $error = 'No hay suficientes boletos disponibles';
        } else {
            $total = $quantity * (float)$t['price'];

            $ins = $db->prepare(
                'INSERT INTO registrations 
                 (user_id, event_id, ticket_id, quantity, total, status, created_at) 
                 VALUES (:u, :e, :t, :q, :total, :st, datetime("now"))'
            );
            $ins->execute([
                ':u'     => userId() ?? null,
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

// Renderizar vista
ob_start();
?>
<h2>
    Inscribirme al evento #<?= intval($event['id'] ?? $eventId) ?> — 
    <?= e($event['title'] ?? '') ?>
</h2>

<?php if (!$event): ?>
    <p class="danger">Evento no encontrado o no publicado.</p>
    <p><a href="<?= e(BASE_URL) ?>/index.php?view=events">⬅️ Volver</a></p>
<?php else: ?>
    <?php if (!empty($error)): ?>
        <p style="color:#b00"><?= e($error) ?></p>
    <?php endif; ?>

    <form action="<?= e(BASE_URL) ?>/index.php?view=registration&event_id=<?= intval($eventId) ?>" method="post">
        <input type="hidden" name="csrf" value="<?= e(generate_csrf()) ?>">

        <label for="ticket_id">Tipo de entrada:</label>
        <select id="ticket_id" name="ticket_id" required>
            <option value="">— Selecciona —</option>
            <?php foreach ($tickets as $t): ?>
                <option value="<?= intval($t['id']) ?>">
                    <?= e($t['name']) ?> ($<?= e(number_format((float)$t['price'], 2)) ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="quantity">Cantidad:</label>
        <input id="quantity" type="number" name="quantity" min="1" value="1" required>

        <button type="submit">Comprar / Reservar</button>
    </form>

    <p><a href="<?= e(BASE_URL) ?>/index.php?view=events">Volver</a></p>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
