<?php
// Obtener ID del evento desde la URL
$eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Consultar tickets del evento con cantidad reservada
$stmt = $db->prepare(
    'SELECT t.*, 
            COALESCE(
                (SELECT SUM(quantity) 
                 FROM registrations r 
                 WHERE r.ticket_id = t.id 
                   AND r.status IN ("pending","confirmed")
                ), 0
            ) AS reserved 
     FROM tickets t 
     WHERE t.event_id = :eid'
);
$stmt->execute([':eid' => $eventId]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicia el buffer de salida
ob_start();
?>
<h2>Tickets del evento #<?= intval($eventId) ?></h2>

<p>
    <a href="<?= e(BASE_URL) ?>/index.php?view=tickets_form&event_id=<?= intval($eventId) ?>">Crear ticket</a> |
    <a href="<?= e(BASE_URL) ?>/index.php?view=events">Volver a eventos</a>
</p>

<?php if (empty($tickets)): ?>
    <p>No hay tickets creados.</p>
<?php else: ?>
    <?php foreach ($tickets as $t): 
        $available = max(0, (int)$t['issued'] - (int)$t['reserved']); ?>
        
        <article style="border-bottom:1px solid #eee; padding:10px 0;">
            <h3><?= e($t['name']) ?></h3>
            <p>Precio: $<?= e(number_format((float)$t['price'], 2)) ?></p>
            <p>Emitidas: <?= intval($t['issued']) ?></p>
            <p>Disponibles: <?= intval($available) ?></p>
        </article>
    <?php endforeach; ?>
<?php endif; ?>

<?php
// Captura el contenido y lo pasa al layout
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>