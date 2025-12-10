<?php 
// Iniciamos el buffer de salida
ob_start(); 
?>
<h2>Tickets del evento #<?php echo htmlspecialchars($event_id); ?></h2>

<p>
  <a href="index.php?view=create_ticket&event_id=<?php echo intval($event_id); ?>" class="btn">Crear ticket</a>
  <a href="index.php?view=events" class="btn">Volver a eventos</a>
</p>

<div class="event-list">
  <?php if (!empty($tickets)): foreach ($tickets as $t): ?>
    <article class="event-item">
      <h3><?php echo htmlspecialchars($t['name']); ?></h3>
      <p><strong>Precio:</strong> $<?php echo number_format($t['price'],2); ?></p>
      <p><strong>Emitidas:</strong> <?php echo intval($t['quantity']); ?></p>
      <?php $rem = (new Ticket())->remaining($t['id']); ?>
      <p><strong>Disponibles:</strong> <?php echo $rem; ?></p>

      <a href="index.php?action=delete_ticket&id=<?php echo intval($t['id']); ?>&event_id=<?php echo intval($event_id); ?>" 
         class="btn btn-delete-ticket">🗑 Eliminar</a>
    </article>
  <?php endforeach; else: ?>
    <p>No hay tickets creados.</p>
  <?php endif; ?>
</div>
<?php
// Guardamos el contenido del buffer en la variable $content
$content = ob_get_clean();

// Incluimos el layout principal
require 'views/layout.php';
?>
