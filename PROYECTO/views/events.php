<?php
ob_start();
?>

<h1>Lista de Eventos</h1>

<?php if (empty($events)): ?>

    <p>No hay eventos disponibles.</p>

<?php else: ?>

    <?php foreach ($events as $e): ?>
        <div style="margin-bottom:20px; padding:10px; border:1px solid #ccc;">
            <h3><?= htmlspecialchars($e['title']) ?></h3>

            <p><strong>Inicio:</strong> <?= htmlspecialchars($e['start_datetime']) ?></p>
            <p><strong>Fin:</strong> <?= htmlspecialchars($e['end_datetime']) ?></p>

            <p><strong>Capacidad total:</strong> <?= intval($e['capacity']) ?></p>
            <p><strong>Tickets disponibles:</strong> <?= intval($e['tickets_available']) ?></p>
            <p><strong>Precio:</strong> $<?= number_format($e['price'], 2) ?></p>

            <!-- CORREGIDO: cambiar id= por event_id= -->
            <a href="index.php?view=registration_form&event_id=<?= intval($e['id']) ?>">Ver detalles</a>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
