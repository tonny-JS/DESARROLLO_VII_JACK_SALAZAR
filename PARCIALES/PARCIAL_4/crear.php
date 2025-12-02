<?php
include "database.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $categoria = trim($_POST["categoria"]);
    $precio = $_POST["precio"];
    $cantidad = $_POST["cantidad"];

    if ($nombre != "" && $categoria != "" && is_numeric($precio) && is_numeric($cantidad)) {
        $sql = "INSERT INTO productos (nombre, categoria, precio, cantidad) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nombre, $categoria, $precio, $cantidad]);
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
    Nombre: <input type="text" name="nombre"><br>
    Categoría: <input type="text" name="categoria"><br>
    Precio: <input type="number" step="0.01" name="precio"><br>
    Cantidad: <input type="number" name="cantidad"><br>
    <button type="submit">Guardar</button>
</form>
</body>
</html>