<?php
require_login();
$uid = userId();

$eventId = intval($_GET['event_id'] ?? 0);
$event = null;

if ($eventId > 0) {
    $stmt = $db->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);

    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $start = trim($_POST['start'] ?? '');
    $end   = trim($_POST['end'] ?? '');
    $cap   = $_POST['capacity'] !== '' ? (int)$_POST['capacity'] : null;
    $price = $_POST['price'] !== '' ? (float)$_POST['price'] : 0.0;
    $state = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';

    if ($title === '' || $start === '' || $end === '') {
        $error = 'Completa título y fechas';
    } elseif (strtotime($end) < strtotime($start)) {
        $error = 'La fecha fin debe ser mayor a inicio';
    } else {

        // EDITAR EVENTO
        if ($eventId) {
            $upd = $db->prepare(
                'UPDATE events SET
                    title = :t,
                    description = :d,
                    start_datetime = :s,
                    end_datetime   = :e,
                    capacity       = :c,
                    price          = :p,
                    status         = :st
                 WHERE id = :id'
            );
            $upd->execute([
                ':t' => $title,
                ':d' => $desc,
                ':s' => $start,
                ':e' => $end,
                ':c' => $cap,
                ':p' => $price,
                ':st' => $state,
                ':id' => $eventId
            ]);

            header('Location: ' . BASE_URL . '/index.php?view=organizer_dashboard');
            exit;
        }

        // CREAR EVENTO
        else {
            $org = $db->prepare('SELECT id FROM organizers WHERE user_id = :uid LIMIT 1');
            $org->execute([':uid' => $uid]);
            $orgRow = $org->fetch(PDO::FETCH_ASSOC);

            if (!$orgRow) {
                $error = 'No sos organizador';
            } else {
                $ins = $db->prepare(
                    'INSERT INTO events 
                        (organizer_id, title, description, start_datetime, end_datetime, capacity, price, status)
                     VALUES 
                        (:o, :t, :d, :s, :e, :c, :p, :st)'
                );
                $ins->execute([
                    ':o' => $orgRow['id'],
                    ':t' => $title,
                    ':d' => $desc,
                    ':s' => $start,
                    ':e' => $end,
                    ':c' => $cap,
                    ':p' => $price,
                    ':st' => $state
                ]);

                $eventId = (int)$db->lastInsertId();
                header('Location: ' . BASE_URL . "/index.php?view=tickets&event_id={$eventId}");
                exit;
            }
        }
    }
}

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
               value="<?= e(isset($event['start_datetime']) ? date('Y-m-d\\TH:i', strtotime($event['start_datetime'])) : '') ?>">
    </label><br>

    <label>
        Fin:<br>
        <input type="datetime-local" name="end" required
               value="<?= e(isset($event['end_datetime']) ? date('Y-m-d\\TH:i', strtotime($event['end_datetime'])) : '') ?>">
    </label><br>

    <label>
        Capacidad:<br>
        <input type="number" name="capacity" min="0" value="<?= e((string)($event['capacity'] ?? '')) ?>">
    </label><br>

    <label>
        Precio general:<br>
        <input type="number" name="price" step="0.01" min="0"
               value="<?= e((string)($event['price'] ?? '0')) ?>">
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
