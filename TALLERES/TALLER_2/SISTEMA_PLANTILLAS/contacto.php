<?php
$paginaActual = 'contacto';
require_once 'plantillas/funciones.php';
$tituloPagina = obtenerTituloPagina($paginaActual);
include 'plantillas/encabezado.php';
?>

<h2>Contacto</h2>
<p>Formulario o información de contacto.</p>

<?php
include 'plantillas/pie_pagina.php';
?>