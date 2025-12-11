<?php
$paginaActual = 'sobre_nosotros';
require_once 'plantillas/funciones.php';
$tituloPagina = obtenerTituloPagina($paginaActual);
include 'plantillas/encabezado.php';
?>

<h2>Sobre Nosotros</h2>
<p>Información sobre nuestra organización.</p>

<?php
include 'plantillas/pie_pagina.php';
?>