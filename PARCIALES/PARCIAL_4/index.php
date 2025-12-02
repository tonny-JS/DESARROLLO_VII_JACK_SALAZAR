<?php
include "database.php";

$stmt = $conn->query("SELECT * FROM productos ORDER BY id DESC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Listado de Productos</title>
</head>
<body>
<h1>Productos</h1>
<a href="crear.php">Nuevo producto</a>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Cantidad</th><th>Fecha</th><th>Acciones</th>
    </tr>
    <?php foreach($productos as $p): ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['nombre']) ?></td>
        <td><?= htmlspecialchars($p['categoria']) ?></td>
        <td><?= $p['precio'] ?></td>
        <td><?= $p['cantidad'] ?></td>
        <td><?= $p['fecha_registro'] ?></td>
        <td>
            <a href="editar.php?id=<?= $p['id'] ?>">Editar</a> |
            <a href="eliminar.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Seguro?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
