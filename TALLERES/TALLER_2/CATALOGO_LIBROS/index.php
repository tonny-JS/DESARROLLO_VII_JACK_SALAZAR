<?php
// Incluir archivos necesarios
require 'includes/funciones.php';
include 'includes/header.php';

// Obtener lista de libros
$libros = obtenerLibros();

// Mostrar libros
foreach ($libros as $libro) {
    echo mostrarDetallesLibro($libro);
}

include 'includes/footer.php';
?>
