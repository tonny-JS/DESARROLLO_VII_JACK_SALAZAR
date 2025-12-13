<?php
require_login();
$uid = userId();

$eventId = intval($_GET['event_id'] ?? 0);
if ($eventId <= 0) {
    echo "<h3>ID de evento inválido</h3>";
    exit;
}

// Obtener evento
$stmt = $db->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "<h3>Evento no encontrado</h3>";
    exit;
}

// Verificar que el usuario es organizador del evento
$org = $db->prepare(
    'SELECT o.id FROM organizers o
     JOIN events e ON e.organizer_id = o.id
     WHERE e.id = :eid AND o.user_id = :uid
     LIMIT 1'
);
$org->execute([':eid' => $eventId, ':uid' => $uid]);
$orgRow = $org->fetch(PDO::FETCH_ASSOC);

if (!$orgRow) {
    echo "<h3>No tienes permiso para administrar tickets de este evento</h3>";
    exit;
}

// Crear ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);

    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $qty = (int)($_POST['quantity'] ?? 0);

    if ($name === '' || $qty <= 0) {
        $error = "Nombre del ticket y cantidad son obligatorios.";
    } else {
        $ins = $db->prepare(
            'INSERT INTO tickets (event_id, name, price, quantity)
             VALUES (:eid, :n, :p, :q)'
        );
        $ins->execute([
            ':eid' => $eventId,
            ':n' => $name,
            ':p' => $price,
            ':q' => $qty
        ]);

        header("Location: " . BASE_URL . "/index.php?view=tickets&event_id=$eventId");
        exit;
    }
}

// Listar tickets
$tickets = $db->prepare('SELECT * FROM tickets WHERE event_id = :eid');
$tickets->execute([':eid' => $eventId]);
$rows = $tickets->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<h2>Tickets del evento: <?= e($event['title']) ?></h2>

<a href="<?= e(BASE_URL) ?>/index.php?view=organizer_dashboard">Volver al panel</a>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= e($error) ?></p>
<?php endif; ?>

<h3>Crear ticket</h3>

<form method="post">
    <input type="hidden" name="csrf" value="<?= e(generate_csrf()) ?>">

    <label>
        Nombre del ticket:<br>
        <input type="text" name="name" required>
    </label><br>

    <label>
        Precio:<br>
        <input type="number" step="0.01" name="price" min="0" value="0">
    </label><br>

    <label>
        Cantidad disponible:<br>
        <input type="number" name="quantity" min="1" required>
    </label><br>

    <button type="submit">Crear ticket</button>
</form>

<hr>

<h3>Tickets existentes</h3>

<?php if (!$rows): ?>
    <p>No hay tickets creados.</p>
<?php else: ?>
    <?php foreach ($rows as $t): ?>
        <div style="border-bottom:1px solid #ccc; padding:10px 0;">
            <strong><?= e($t['name']) ?></strong><br>
            Precio: $<?= e(number_format($t['price'], 2)) ?><br>
            Cantidad: <?= intval($t['quantity']) ?><br>

            <a href="<?= e(BASE_URL) ?>/index.php?view=tickets_edit&id=<?= $t['id'] ?>">Editar</a> |
            <a href="<?= e(BASE_URL) ?>/index.php?view=tickets_delete&id=<?= $t['id'] ?>"
                onclick="return confirm('¿Eliminar este ticket?');">
                Eliminar
            </a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
