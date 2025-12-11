<?php
// Requiere que el usuario esté autenticado y sea organizador
require_login();
$uid = userId();

if (!is_organizer($db, $uid)) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// Inicializar resumen
$summary = [
    'events'            => 0,
    'registrations'     => 0,
    'confirmed_tickets' => 0,
    'revenue'           => 0.0
];
$events = [];

// Total de eventos del organizador
$stmt = $db->prepare(
    'SELECT COUNT(*) 
     FROM events 
     WHERE organizer_id IN (SELECT id FROM organizers WHERE user_id = :uid)'
);
$stmt->execute([':uid' => $uid]);
$summary['events'] = (int)$stmt->fetchColumn();

// Tickets confirmados e ingresos
$stmt = $db->prepare(
    'SELECT COALESCE(SUM(r.quantity), 0), COALESCE(SUM(r.total), 0) 
     FROM registrations r 
     JOIN events e ON e.id = r.event_id 
     WHERE e.organizer_id IN (SELECT id FROM organizers WHERE user_id = :uid) 
       AND r.status = "confirmed"'
);
$stmt->execute([':uid' => $uid]);
list($summary['confirmed_tickets'], $summary['revenue']) = $stmt->fetch(PDO::FETCH_NUM);

// Eventos con estadísticas
$stmt = $db->prepare(
    'SELECT e.id, e.title, e.capacity,
            COALESCE(
                (SELECT SUM(quantity) 
                 FROM registrations r 
                 WHERE r.event_id = e.id 
                   AND r.status IN ("pending","confirmed")
                ), 0
            ) AS reserved,
            COALESCE(
                (SELECT COUNT(*) 
                 FROM registrations r 
                 WHERE r.event_id = e.id
                ), 0
            ) AS regs
     FROM events e
     WHERE e.organizer_id IN (SELECT id FROM organizers WHERE user_id = :uid)
     ORDER BY e.start_at DESC'
);
$stmt->execute([':uid' => $uid]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Renderizar vista
ob_start();
?>
<h2>Panel de Organizador</h2>

<section>
    <h3>Resumen</h3>
    <p>Eventos: <?= intval($summary['events']) ?></p>
    <p>Inscripciones (confirmadas): <?= intval($summary['confirmed_tickets']) ?></p>
    <p>Ingresos confirmados: $<?= e(number_format((float)$summary['revenue'], 2)) ?></p>
</section>

<section>
    <h3>Por evento</h3>
    <?php if (empty($events)): ?>
        <p>No tienes eventos aún.</p>
    <?php else: ?>
        <?php foreach ($events as $ev): ?>
            <article style="border-bottom:1px solid #eee; padding:10px 0;">
                <h4>(ID: <?= intval($ev['id']) ?>) <?= e($ev['title']) ?></h4>
                <p>Capacidad: <?= intval($ev['capacity']) ?></p>
                <p>Reservadas (pend+conf): <?= intval($ev['reserved']) ?></p>
                <p>Inscripciones: <?= intval($ev['regs']) ?></p>
                <p>
                    <a href="<?= e(BASE_URL) ?>/index.php?view=tickets&event_id=<?= intval($ev['id']) ?>">
                        Tickets
                    </a>
                </p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>