<?php
require_login();
$uid = userId();

$ticketId = intval($_GET['id'] ?? 0);
if ($ticketId <= 0) {
    echo "<h3>ID de ticket inválido</h3>";
    exit;
}

// Obtener ticket
$stmt = $db->prepare('SELECT * FROM tickets WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $ticketId]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo "<h3>Ticket no encontrado</h3>";
    exit;
}

$eventId = $ticket['event_id'];

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
    echo "<h3>No tienes permiso para editar este ticket</h3>";
    exit;
}

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);

    $name  = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $qty   = (int)($_POST['quantity'] ?? 0);

    if ($name === '' || $qty <= 0) {
        $error = "El nombre y la cantidad son obligatorios.";
    } else {
        $upd = $db->prepare(
            'UPDATE tickets SET
                name = :n,
                price = :p,
                quantity = :q
             WHERE id = :id'
        );

        $upd->execute([
            ':n' => $name,
            ':p' => $price,
            ':q' => $qty,
            ':id' => $ticketId
        ]);

        header("Location: " . BASE_URL . "/index.php?view=tickets&event_id=$eventId");
        exit;
    }
}

ob_start();
?>

<h2>Editar ticket</h2>

<a href="<?= e(BASE_URL) ?>/index.php?view=tickets&event_id=<?= $eventId ?>">⬅ Volver</a>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= e($error) ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= e(generate_csrf()) ?>">

    <label>
        Nombre del ticket:<br>
        <input type="text" name="name" required value="<?= e($ticket['name']) ?>">
    </label><br>

    <label>
        Precio:<br>
        <input type="number" step="0.01" name="price" min="0" value="<?= e($ticket['price']) ?>">
    </label><br>

    <label>
        Cantidad disponible:<br>
        <input type="number" name="quantity" min="1" required value="<?= e($ticket['quantity']) ?>">
    </label><br>

    <button type="submit">Guardar cambios</button>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
