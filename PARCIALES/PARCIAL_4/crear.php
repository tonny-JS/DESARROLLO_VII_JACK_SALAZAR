<?php
include "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $categoria = trim($_POST["categoria"]);
    $precio = $_POST["precio"];
    $cantidad = $_POST["cantidad"];

    if ($nombre != "" && $categoria != "" && is_numeric($precio) && is_numeric($cantidad)) {
        $sql = "INSERT INTO productos (nombre, categoria, precio, cantidad) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdi", $nombre, $categoria, $precio, $cantidad);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Nuevo Producto</title></head>
<body>
<h1>Registrar Producto</h1>
<form method="post">
    Nombre: <input type="text" name="nombre" required><br>
    Categoría: <input type="text" name="categoria" required><br>
    Precio: <input type="number" step="0.01" name="precio" required><br>
    Cantidad: <input type="number" name="cantidad" required><br>
    <button type="submit">Guardar</button>
</form>
</body>
</html>
