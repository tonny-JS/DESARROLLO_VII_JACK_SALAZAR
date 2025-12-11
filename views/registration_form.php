<?php 
// Iniciamos el buffer de salida
ob_start(); 
?>
<div class="registration-form">
    <h2>Inscribirme al evento #<?php echo htmlspecialchars($event_id); ?></h2>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="index.php?action=store_registration">
        <input type="hidden" name="event_id" value="<?php echo intval($event_id); ?>">

        <label for="ticket_id">Tipo de entrada:</label>
        <select id="ticket_id" name="ticket_id" required>
            <option value="">-- Selecciona --</option>
            <?php foreach ($tickets as $t): ?>
                <option value="<?php echo intval($t['id']); ?>">
                    <?php echo htmlspecialchars($t['name']); ?> 
                    ($<?php echo number_format($t['price'],2); ?>)
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="quantity">Cantidad:</label>
        <input type="number" id="quantity" name="quantity" min="1" value="1" required><br>

        <button class="btn" type="submit">Comprar / Reservar</button>
    </form>

    <p>
        <a href="index.php?view=events" class="btn">Volver</a>
    </p>
</div>
<?php
// Guardamos el contenido del buffer en la variable $content
$content = ob_get_clean();

// Incluimos el layout principal
require 'views/layout.php';
?>
