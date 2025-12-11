<?php
ob_start(); // iniciar buffer
?>

<h1>Lista de Eventos</h1>
<p>Aquí aparecerán los eventos disponibles.</p>

<?php
$content = ob_get_clean(); // obtener contenido
include __DIR__ . '/layout.php'; // cargar layout
