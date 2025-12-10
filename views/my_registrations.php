<?php 
// Iniciamos el buffer de salida
ob_start(); 
?>
<h2>Mis inscripciones</h2>

<?php if (!empty($_GET['ok'])): ?>
  <p style="color:green">Inscripción creada correctamente (estado: pendiente).</p>
<?php endif; ?>

<div class="event-list">
  <?php if (!empty($regs)): foreach ($regs as $r): ?>
    <article class="event-item">
      <h3><?php echo htmlspecialchars($r['title']); ?> — <?php echo htmlspecialchars($r['ticket_name']); ?></h3>
      <p><strong>Cantidad:</strong> <?php echo intval($r['quantity']); ?></p>
      <p><strong>Total:</strong> $<?php echo number_format($r['total_price'],2); ?></p>
      <p><strong>Estado:</strong> <?php echo htmlspecialchars($r['status']); ?></p>

      <a href="index.php?action=cancel_registration&id=<?php echo intval($r['id']); ?>" 
         class="btn btn-cancel-registration">Cancelar</a>
    </article>
  <?php endforeach; else: ?>
    <p>No tienes inscripciones.</p>
  <?php endif; ?>
</div>
<?php
// Guardamos el contenido del buffer en la variable $content
$content = ob_get_clean();

// Incluimos el layout principal
require 'views/layout.php';
?>
