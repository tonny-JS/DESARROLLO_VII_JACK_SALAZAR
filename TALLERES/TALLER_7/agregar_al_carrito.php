<?php include 'config_sesion.php';

$productos = [
    1 => ['nombre' => 'Laptop', 'precio' => 800],
    2 => ['nombre' => 'Mouse', 'precio' => 20],
    3 => ['nombre' => 'Teclado', 'precio' => 35],
    4 => ['nombre' => 'Monitor', 'precio' => 150],
    5 => ['nombre' => 'USB', 'precio' => 10]
];

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id && isset($productos[$id])) {
    if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];
    if (!isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id] = ['producto' => $productos[$id], 'cantidad' => 1];
    } else {
        $_SESSION['carrito'][$id]['cantidad']++;
    }
}
header("Location: ver_carrito.php");
exit();
?>