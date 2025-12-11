<?php
// Requiere que el usuario esté autenticado
require_login();
$uid = userId();

// Consultar inscripciones del usuario
$stmt = $db->prepare(
    'SELECT r.id, r.quantity, r.total, r.status, e.title
     FROM registrations r
     JOIN events e ON e.id = r.event_id
     WHERE r.user_id = :uid
     ORDER BY r.created_at DESC'
);
$stmt->execute([':uid' => $uid]);
$regs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicia el buffer de salida
ob_start();
?>
<h2>Mis inscripciones</h2>

<?php if (empty($regs)): ?>
    <p>No tienes inscripciones.</p>
<?php else: ?>
    <?php foreach ($regs as $r): ?>
        <section style="border-bottom:1px solid #eee; padding:10px 0;">
            <h3><?= e($r['title']) ?></h3>
            <p><strong>Cantidad:</strong> <?= intval($r['quantity']) ?></p>
            <p><strong>Total:</strong> $<?= e(number_format((float)$r['total'], 2)) ?></p>
            <p><strong>Estado:</strong> <?= e($r['status']) ?></p>

            <?php if ($r['status'] !== 'cancelled'): ?>
                <p>
                    <a href="<?= e(BASE_URL) ?>/index.php?action=cancel_registration&id=<?= intval($r['id']) ?>"
                       onclick="return confirm('¿Cancelar esta inscripción?');">
                        Cancelar
                    </a>
                </p>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
<?php endif; ?>

<?php
// Captura el contenido y lo pasa al layout
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>