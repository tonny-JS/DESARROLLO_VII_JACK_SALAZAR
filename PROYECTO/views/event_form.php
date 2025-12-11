<?php 
// Iniciamos el buffer de salida
ob_start(); 
?>
<div class="event-form">
    <h2>Crear Evento</h2>

    <form method="post" action="index.php?action=store_event">
        <input type="hidden" name="organizer_id" value="<?php echo htmlspecialchars($organizer_id); ?>">

        <label for="title">Título:</label>
        <input type="text" id="title" name="title" placeholder="Título" required><br>

        <label for="description">Descripción:</label>
        <textarea id="description" name="description" placeholder="Descripción"></textarea><br>

        <label for="start_datetime">Inicio:</label>
        <input type="datetime-local" id="start_datetime" name="start_datetime" required><br>

        <label for="end_datetime">Fin:</label>
        <input type="datetime-local" id="end_datetime" name="end_datetime"><br>

        <label for="capacity">Capacidad (si vacío, usa capacidad de la sede):</label>
        <input type="number" id="capacity" name="capacity" placeholder="Capacidad"><br>

        <label for="price">Precio general:</label>
        <input type="number" id="price" name="price" step="0.01" placeholder="Precio" value="0.00"><br>

        <label for="status">Estado:</label>
        <select id="status" name="status">
            <option value="draft">Borrador</option>
            <option value="published">Publicado</option>
        </select><br>

        <button class="btn" type="submit">Guardar</button>
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