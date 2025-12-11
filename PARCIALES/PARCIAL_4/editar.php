<?php
include "database.php";

$id = $_GET["id"] ?? null;
if (!$id) { header("Location: index.php"); exit; }

$sql = "SELECT * FROM productos WHERE id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$producto = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $categoria = trim($_POST["categoria"]);
    $precio = $_POST["precio"];
    $cantidad = $_POST["cantidad"];

    $sql = "UPDATE productos SET nombre=?, categoria=?, precio=?, cantidad=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdii", $nombre, $categoria, $precio, $cantidad, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
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
    Nombre: <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required><br>
    Categoría: <input type="text" name="categoria" value="<?= htmlspecialchars($producto['categoria']) ?>" required><br>
    Precio: <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required><br>
    Cantidad: <input type="number" name="cantidad" value="<?= $producto['cantidad'] ?>" required><br>
    <button type="submit">Actualizar</button>
</form>
</body>
</html>
