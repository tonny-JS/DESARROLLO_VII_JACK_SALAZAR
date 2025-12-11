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

            <p><strong>Inicio:</strong> <?= $e['start_date'] ?></p>
            <p><strong>Fin:</strong> <?= $e['end_date'] ?></p>

            <p><strong>Tickets disponibles:</strong> <?= $e['tickets_available'] ?></p>

            <a href="index.php?view=registration_form&id=<?= $e['id'] ?>">Ver detalles</a>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
