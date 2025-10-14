<?php include 'config_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Productos</title></head>
<body>
<h2>Lista de Productos</h2>
<ul>
    <?php
    $productos = [
        1 => ['nombre' => 'Laptop', 'precio' => 800],
        2 => ['nombre' => 'Mouse', 'precio' => 20],
        3 => ['nombre' => 'Teclado', 'precio' => 35],
        4 => ['nombre' => 'Monitor', 'precio' => 150],
        5 => ['nombre' => 'USB', 'precio' => 10]
    ];
    foreach ($productos as $id => $producto) {
        echo "<li>" . htmlspecialchars($producto['nombre']) . " - $" . $producto['precio'] .
            " <a href='agregar_al_carrito.php?id=$id'>Añadir al carrito</a></li>";
    }
    ?>
</ul>
<a href="ver_carrito.php">Ver Carrito</a>
</body>
</html>
