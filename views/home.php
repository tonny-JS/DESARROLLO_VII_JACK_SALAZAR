<?php
// Inicia el buffer de salida
ob_start();
?>

<h1>Bienvenido a la Plataforma de Eventos</h1>
<p>Explora los eventos disponibles.</p>

<?php
// Captura el contenido generado y lo guarda en la variable $content
$content = ob_get_clean();

// Incluye el layout principal
require __DIR__ . '/layout.php';

