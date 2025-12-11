<?php include 'config_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Productos</title></head>
<body>
<h2>Selecciona tus productos</h2>
<form method="post" action="agregar_al_carrito.php">
<?php
$productos = json_decode(file_get_contents('productos.json'), true);

foreach ($productos as $producto) {
    $id = $producto['id'];
    $nombre = htmlspecialchars($producto['nombre']);
    $precio = $producto['precio'];
    echo "<p>$nombre - $$precio 
          <label>Cantidad: <input type='number' name='cantidades[$id]' value='0' min='0'></label></p>";
}
?>
<input type="submit" value="Añadir al carrito">
</form>
<a href="ver_carrito.php">Ver Carrito</a>
</body>
</html>
