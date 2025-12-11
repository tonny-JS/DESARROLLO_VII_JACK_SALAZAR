<?php 
// Iniciamos el buffer de salida
ob_start(); 
?>
<div class="tickets-form">
    <h2>Crear Ticket para el evento #<?php echo htmlspecialchars($event_id); ?></h2>

    <form method="post" action="index.php?action=store_ticket">
        <input type="hidden" name="event_id" value="<?php echo intval($event_id); ?>">

        <label for="name">Nombre del ticket:</label>
        <input type="text" id="name" name="name" placeholder="Ejemplo: General / VIP" required><br>

        <label for="price">Precio:</label>
        <input type="number" id="price" name="price" step="0.01" placeholder="Precio" required><br>

        <label for="quantity">Cantidad emitida:</label>
        <input type="number" id="quantity" name="quantity" placeholder="Cantidad" required><br>

        <button class="btn" type="submit">Guardar</button>
    </form>

    <p>
        <a href="index.php?view=tickets&event_id=<?php echo intval($event_id); ?>" class="btn">Volver</a>
    </p>
</div>
<?php
// Guardamos el contenido del buffer en la variable $content
$content = ob_get_clean();

// Incluimos el layout principal
require 'views/layout.php';
?>
