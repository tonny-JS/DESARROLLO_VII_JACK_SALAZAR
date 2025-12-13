<?php 
require_login();
$uid = userId();

// Consulta correcta usando tu BD REAL
$stmt = $db->prepare("
    SELECT 
        r.id,
        r.quantity,
        r.total_price,
        r.status,
        e.title,
        e.start_datetime,
        e.end_datetime,
        e.price as event_price
    FROM registrations r
    JOIN events e ON e.id = r.event_id
    WHERE r.user_id = :uid
    ORDER BY r.created_at DESC
");
$stmt->execute([':uid' => $uid]);
$regs = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<h2>Mis inscripciones</h2>

<?php if (empty($regs)): ?>
    <p>No tienes inscripciones.</p>
<?php else: ?>
    <?php foreach ($regs as $r): ?>
        <section style="border-bottom:1px solid #eee; padding:10px 0;">
            <h3><?= e($r['title']) ?></h3>

            <p><strong>Fecha Inicio:</strong> <?= e($r['start_datetime']) ?></p>
            <p><strong>Cantidad:</strong> <?= intval($r['quantity']) ?></p>
            <p><strong>Total Pagado:</strong> $<?= number_format((float)$r['total_price'], 2) ?></p>
            <p><strong>Estado:</strong> <?= e($r['status']) ?></p>

            <?php if ($r['status'] !== 'cancelled'): ?>
                <p>
                    <a href="<?= e(BASE_URL) ?>/index.php?action=cancel_registration&id=<?= intval($r['id']) ?>"
                       onclick="return confirm('¿Cancelar esta inscripción?');">
                        Cancelar inscripción
                    </a>
                </p>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
