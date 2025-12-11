<?php
// Obtener evento
$event = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
if (!$event) {
    http_response_code(404);
    exit('Evento no encontrado');
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);

    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $start = trim($_POST['start'] ?? '');
    $end   = trim($_POST['end'] ?? '');
    $cap   = $_POST['capacity'] !== '' ? (int)$_POST['capacity'] : null;
    $price = $_POST['base_price'] !== '' ? (float)$_POST['base_price'] : 0.0;
    $state = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';

    // Validaciones
    if ($title === '' || $start === '' || $end === '') {
        $error = 'Completa título y fechas';
    } elseif (strtotime($end) < strtotime($start)) {
        $error = 'La fecha fin debe ser mayor a inicio';
    } else {
        if ($eventId) {
            // Actualizar evento existente
            $upd = $db->prepare(
                'UPDATE events 
                 SET title=:t, description=:d, start_at=:s, end_at=:e, 
                     capacity=:c, base_price=:p, status=:st 
                 WHERE id=:id'
            );
            $upd->execute([
                ':t'  => $title,
                ':d'  => $desc,
                ':s'  => $start,
                ':e'  => $end,
                ':c'  => $cap,
                ':p'  => $price,
                ':st' => $state,
                ':id' => $eventId
            ]);

            header('Location: ' . BASE_URL . '/index.php?view=organizer_dashboard');
            exit;
        } else {
            // Crear nuevo evento
            $orgId = $db->prepare('SELECT id FROM organizers WHERE user_id=:uid LIMIT 1');
            $orgId->execute([':uid' => $uid]);
            $org = $orgId->fetch(PDO::FETCH_ASSOC);

            if (!$org) {
                $error = 'No sos organizador';
            } else {
                $ins = $db->prepare(
                    'INSERT INTO events 
                     (organizer_id, title, description, start_at, end_at, capacity, base_price, status) 
                     VALUES (:o, :t, :d, :s, :e, :c, :p, :st)'
                );
                $ins->execute([
                    ':o'  => $org['id'],
                    ':t'  => $title,
                    ':d'  => $desc,
                    ':s'  => $start,
                    ':e'  => $end,
                    ':c'  => $cap,
                    ':p'  => $price,
                    ':st' => $state
                ]);

                $eventId = (int)$db->lastInsertId();
                header('Location: ' . BASE_URL . "/index.php?view=tickets&event_id={$eventId}");
                exit;
            }
        }
    }
}

// Renderizar vista
ob_start();
?>
<h3><?= $eventId ? 'Editar evento' : 'Crear Evento' ?></h3>

<?php if (!empty($error)): ?>
    <p style="color:#b00"><?= e($error) ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= e(generate_csrf()) ?>">

    <label>
        Título:<br>
        <input type="text" name="title" required value="<?= e($event['title'] ?? '') ?>">
    </label><br>

    <label>
        Descripción:<br>
        <textarea name="description" rows="4"><?= e($event['description'] ?? '') ?></textarea>
    </label><br>

    <label>
        Inicio:<br>
        <input type="datetime-local" name="start" required 
               value="<?= e(isset($event['start_at']) ? date('Y-m-d\TH:i', strtotime($event['start_at'])) : '') ?>">
    </label><br>

    <label>
        Fin:<br>
        <input type="datetime-local" name="end" required 
               value="<?= e(isset($event['end_at']) ? date('Y-m-d\TH:i', strtotime($event['end_at'])) : '') ?>">
    </label><br>

    <label>
        Capacidad (si vacío, usa capacidad de la sede):<br>
        <input type="number" name="capacity" min="0" value="<?= e((string)($event['capacity'] ?? '')) ?>">
    </label><br>

    <label>
        Precio general:<br>
        <input type="number" name="base_price" step="0.01" min="0" value="<?= e((string)($event['base_price'] ?? '0')) ?>">
    </label><br>

    <label>
        Estado:<br>
        <select name="status">
            <option value="draft" <?= isset($event['status']) && $event['status'] === 'draft' ? 'selected' : '' ?>>Borrador</option>
            <option value="published" <?= isset($event['status']) && $event['status'] === 'published' ? 'selected' : '' ?>>Publicado</option>
        </select>
    </label><br>

    <button type="submit">Guardar</button>
    <a href="<?= e(BASE_URL) ?>/index.php?view=events">Volver</a>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
