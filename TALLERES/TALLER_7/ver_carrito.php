<?php include 'config_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Carrito</title></head>
<body>
<h2>Tu Carrito</h2>
<?php
$total = 0;
if (!empty($_SESSION['carrito'])) {
    echo "<ul>";
    foreach ($_SESSION['carrito'] as $id => $item) {
        $nombre = htmlspecialchars($item['producto']['nombre']);
        $precio = $item['producto']['precio'];
        $cantidad = $item['cantidad'];
        $subtotal = $precio * $cantidad;
        $total += $subtotal;
        echo "<li>$nombre - $cantidad x $$precio = $$subtotal 
              <a href='eliminar_del_carrito.php?id=$id'>Eliminar</a></li>";
    }
    echo "</ul>";
    echo "<p>Total: $$total</p>";
    echo "<a href='checkout.php'>Finalizar Compra</a>";
} else {
    echo "<p>El carrito está vacío.</p>";
}
?>
<a href="productos.php">Volver a productos</a>
</body>
</html>
