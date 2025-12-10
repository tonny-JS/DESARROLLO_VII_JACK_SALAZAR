<?php 
// Iniciamos el buffer de salida
ob_start(); 
?>
<h2>Eventos publicados</h2>

<?php if (!empty($_SESSION['user'])): ?>
  <?php
    $db = (new Database())->pdo();
    $stmt = $db->prepare('SELECT id FROM organizers WHERE user_id=:uid LIMIT 1');
    $stmt->execute([':uid' => $_SESSION['user']['id']]);
    $org = $stmt->fetch();
  ?>
  <?php if ($org): ?>
    <p><a href="index.php?view=create_event" class="btn">Crear nuevo evento</a></p>
  <?php endif; ?>
<?php endif; ?>

<div class="event-list">
  <?php if (!empty($events)): foreach ($events as $e): ?>
    <article class="event-item <?php echo ($e['status']==='published')?'published':''; ?>">
      <h3><?php echo htmlspecialchars($e['title']); ?></h3>
      <p><strong>Inicio:</strong> <?php echo htmlspecialchars($e['start_datetime']); ?></p>
      <p><strong>Lugar:</strong> <?php echo htmlspecialchars($e['venue_name'] ?? 'Sin sede'); ?></p>
      <p><strong>Precio base:</strong> $<?php echo number_format($e['price'],2); ?></p>

      <a href="index.php?view=register_event&event_id=<?php echo $e['id']; ?>" class="btn">Inscribirme</a>

      <?php if (!empty($org)): ?>
        <a href="index.php?view=tickets&event_id=<?php echo $e['id']; ?>" class="btn">Tickets</a>
        <a href="index.php?action=toggle_event&id=<?php echo $e['id']; ?>" class="btn btn-toggle-event">
          <?php echo $e['status']==='published' ? '✓ Publicado' : '○ Borrador'; ?>
        </a>
        <a href="index.php?action=delete_event&id=<?php echo $e['id']; ?>" class="btn btn-delete-event">🗑 Eliminar</a>
      <?php endif; ?>
    </article>
  <?php endforeach; else: ?>
    <p>No hay eventos publicados.</p>
  <?php endif; ?>
</div>
<?php
// Guardamos el contenido del buffer en la variable $content
$content = ob_get_clean();

// Incluimos el layout principal
require 'views/layout.php';
?>