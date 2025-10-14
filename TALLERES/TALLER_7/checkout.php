<?php 
include 'config_sesion.php';

$productos = [...]; // Igual que antes
$total = 0;

if (!empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $id => $item) {
        $total += $productos[$id]['precio'] * $item['cantidad'];
    }
    $nombre = 'Usuario'; // En producción, usar nombre real
    setcookie('nombre_usuario', $nombre, time() + 86400, '/', '', true, true);
    $_SESSION['carrito'] = [];
}
?>