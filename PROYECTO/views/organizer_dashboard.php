<?php
require_login();
$uid = userId();

// Validar que realmente es organizador
if (!is_organizer($db, $uid)) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// RESUMEN GENERAL
$summary = [
    'events' => 0,
    'confirmed_tickets' => 0,
    'revenue' => 0.0
];

// Número de eventos
$stmt = $db->prepare(
    'SELECT COUNT(*) FROM events 
     WHERE organizer_id IN (SELECT id FROM organizers WHERE user_id = :uid)'
);
$stmt->execute([':uid' => $uid]);
$summary['events'] = (int)$stmt->fetchColumn();

// Tickets confirmados + ingresos confirmados
$stmt = $db->prepare(
    'SELECT 
         COALESCE(SUM(r.quantity), 0),
         COALESCE(SUM(r.total_price), 0)
     FROM registrations r
     JOIN events e ON e.id = r.event_id
     WHERE e.organizer_id IN (SELECT id FROM organizers WHERE user_id = :uid)
       AND r.status = "confirmed"'
);
$stmt->execute([':uid' => $uid]);
list($summary['confirmed_tickets'], $summary['revenue']) = $stmt->fetch(PDO::FETCH_NUM);

// LISTADO DE EVENTOS
$stmt = $db->prepare(
    'SELECT 
        e.id,
        e.title,
        e.capacity,

        -- Pendientes
        (SELECT COUNT(*) FROM registrations r 
         WHERE r.event_id = e.id AND r.status = "pending") AS pending_regs,

        -- Confirmadas
        (SELECT COUNT(*) FROM registrations r 
         WHERE r.event_id = e.id AND r.status = "confirmed") AS confirmed_regs,

        -- Total
        (SELECT COUNT(*) FROM registrations r 
         WHERE r.event_id = e.id) AS total_regs

     FROM events e
     WHERE e.organizer_id IN (SELECT id FROM organizers WHERE user_id = :uid)
     ORDER BY e.start_datetime DESC'
);
$stmt->execute([':uid' => $uid]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h2>Panel de Organizador</h2>

<section>
    <h3>Resumen</h3>
    <p>Eventos: <?= $summary['events'] ?></p>
    <p>Tickets confirmados: <?= $summary['confirmed_tickets'] ?></p>
    <p>Ingresos confirmados: $<?= number_format($summary['revenue'], 2) ?></p>
</section>

<section>
    <h3>Por evento</h3>

    <?php if (empty($events)): ?>
        <p>No tienes eventos aún.</p>

    <?php else: ?>
        <?php foreach ($events as $ev): ?>
            <article style="padding:10px; border-bottom:1px solid #ccc;">

                <h4>(ID: <?= $ev['id'] ?>) <?= htmlspecialchars($ev['title']) ?></h4>

                <p>Pendientes: <?= $ev['pending_regs'] ?></p>
                <p>Confirmadas: <?= $ev['confirmed_regs'] ?></p>
                <p>Total inscripciones: <?= $ev['total_regs'] ?></p>

                <a href="<?= BASE_URL ?>/index.php?view=event_registrations&event_id=<?= $ev['id'] ?>">
                    Ver inscripciones
                </a>

            </article>
        <?php endforeach; ?>
    <?php endif; ?>

</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
