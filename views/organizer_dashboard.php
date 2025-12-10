<?php 
// Iniciamos el buffer de salida
ob_start(); 
?>
<h2>Panel de Organizador</h2>

<?php if (!empty($_GET['access_logged'])): ?>
  <p style="color:green">Acceso marcado correctamente.</p>
<?php endif; ?>

<section class="event-list">
  <article class="event-item">
    <h3>Resumen</h3>
    <p><strong>Eventos:</strong> <?php echo intval($stats['events_total']); ?></p>
    <p><strong>Inscripciones (totales):</strong> <?php echo intval($stats['registrations_total']); ?></p>
    <p><strong>Confirmadas (entradas):</strong> <?php echo intval($stats['confirmed_total']); ?></p>
    <p><strong>Ingresos confirmados:</strong> $<?php echo number_format($stats['revenue_confirmed'],2); ?></p>
  </article>

  <h3>Por evento</h3>
  <?php foreach ($stats['per_event'] as $ev): ?>
    <article class="event-item">
      <h4><?php echo htmlspecialchars($ev['title']); ?> (ID: <?php echo intval($ev['event_id']); ?>)</h4>
      <p><strong>Capacidad:</strong> <?php echo intval($ev['capacity']); ?></p>
      <p><strong>Reservadas (pend+conf):</strong> <?php echo intval($ev['reserved']); ?></p>
      <p><strong>Inscripciones:</strong> <?php echo intval($ev['regs']); ?></p>
      <p>
        <a href="index.php?view=tickets&event_id=<?php echo intval($ev['event_id']); ?>" class="btn">Tickets</a>
        <!-- Acceso: ejemplo rápido (registración id por query real debería venir de escaneo/QR) -->
      </p>
    </article>
  <?php endforeach; ?>
</section>
<?php
// Guardamos el contenido del buffer en la variable $content
$content = ob_get_clean();

// Incluimos el layout principal
require 'views/layout.php';
?>
