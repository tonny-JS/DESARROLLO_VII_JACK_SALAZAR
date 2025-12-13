<?php include 'config_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Resumen de Compra</title></head>
<body>
<h2>Resumen de Compra</h2>
<?php
$total = 0;
if (!empty($_SESSION['carrito'])) {
    echo "<ul>";
    foreach ($_SESSION['carrito'] as $item) {
        $nombre = htmlspecialchars($item['nombre']);
        $precio = $item['precio'];
        $cantidad = $item['cantidad'];
        $subtotal = $precio * $cantidad;
        $total += $subtotal;
        echo "<li>$nombre - $cantidad x $$precio = $$subtotal</li>";
    }
    echo "</ul>";
    echo "<p>Total pagado: $$total</p>";

    // Cookie para recordar al usuario por 24 horas
    setcookie("usuario", "Cliente", [
        'expires' => time() + 86400,
        'path' => '/',
        'secure' => false, // Usa true si estás en HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    // Vaciar carrito
    $_SESSION['carrito'] = [];
} else {
    echo "<p>No hay productos en el carrito.</p>";
}
?>
<a href="productos.php">Volver a productos</a>
</body>
</html>
