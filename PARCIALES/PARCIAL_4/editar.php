<?php
include "database.php";

$id = $_GET["id"] ?? null;
if (!$id) { header("Location: index.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM productos WHERE id=?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $categoria = trim($_POST["categoria"]);
    $precio = $_POST["precio"];
    $cantidad = $_POST["cantidad"];

    $sql = "UPDATE productos SET nombre=?, categoria=?, precio=?, cantidad=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nombre, $categoria, $precio, $cantidad, $id]);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Editar Producto</title></head>
<body>
<h1>Editar Producto</h1>
<form method="post">
    Nombre: <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>"><br>
    Categoría: <input type="text" name="categoria" value="<?= htmlspecialchars($producto['categoria']) ?>"><br>
    Precio: <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>"><br>
    Cantidad: <input type="number" name="cantidad" value="<?= $producto['cantidad'] ?>"><br>
    <button type="submit">Actualizar</button>
</form>
</body>
</html>

