<?php
// Consulta de eventos publicados
$stmt = $db->prepare(
    'SELECT e.*, o.user_id AS owner_user_id 
     FROM events e 
     JOIN organizers o ON o.id = e.organizer_id 
     WHERE e.status = "published" 
     ORDER BY e.start_at DESC'
);
$stmt->execute();

// Obtener todos los eventos
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicia el buffer de salida
ob_start();
?>
<h2>Eventos</h2>

<?php if (empty($events)): ?>
    <p>No hay eventos publicados.</p>
<?php else: ?>
    <?php foreach ($events as $ev): ?>
        <article style="border-bottom:1px solid #eee; padding:10px 0;">
            <h3><?= e($ev['title']) ?></h3>
            <p><?= e($ev['description']) ?></p>
            <p>
                Inicio: <?= e($ev['start_at']) ?> — 
                Fin: <?= e($ev['end_at']) ?>
            </p>
            <p>
                <a href="<?= e(BASE_URL) ?>/index.php?view=registration&event_id=<?= intval($ev['id']) ?>">
                    Inscribirme
                </a>
            </p>
        </article>
    <?php endforeach; ?>
<?php endif; ?>

<?php
// Captura el contenido y lo pasa al layout
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>