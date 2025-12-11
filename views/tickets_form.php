<?php
// Obtener ID del evento desde la URL
$eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Si no hay evento válido, redirigir a la lista de eventos
if ($eventId === 0) {
    header('Location: ' . BASE_URL . '/index.php?view=events');
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);

    $name   = trim($_POST['name'] ?? '');
    $price  = (float)($_POST['price'] ?? 0);
    $issued = (int)($_POST['issued'] ?? 0);

    if ($name === '') {
        $error = 'Nombre requerido';
    } else {
        $ins = $db->prepare(
            'INSERT INTO tickets (event_id, name, price, issued) 
             VALUES (:e, :n, :p, :i)'
        );
        $ins->execute([
            ':e' => $eventId,
            ':n' => $name,
            ':p' => $price,
            ':i' => $issued
        ]);

        header('Location: ' . BASE_URL . "/index.php?view=tickets&event_id={$eventId}");
        exit;
    }
}

// Renderizar vista
ob_start();
?>
<h2>Crear Ticket para el evento #<?= intval($eventId) ?></h2>

<?php if (!empty($error)): ?>
    <p style="color:#b00"><?= e($error) ?></p>
<?php endif; ?>

<form method="post" action="<?= e(BASE_URL) ?>/index.php?view=tickets_form&event_id=<?= intval($eventId) ?>">
    <input type="hidden" name="csrf" value="<?= e(generate_csrf()) ?>">

    <label>
        Nombre del ticket:<br>
        <input type="text" name="name" required>
    </label><br>

    <label>
        Precio:<br>
        <input type="number" step="0.01" name="price" min="0" required>
    </label><br>

    <label>
        Cantidad emitida:<br>
        <input type="number" name="issued" min="0" required>
    </label><br>

    <button type="submit">Guardar</button>
</form>

<p>
    <a href="<?= e(BASE_URL) ?>/index.php?view=tickets&event_id=<?= intval($eventId) ?>">Volver</a>
</p>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
