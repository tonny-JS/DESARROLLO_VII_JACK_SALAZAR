<?php include 'config_sesion.php';

$productos = json_decode(file_get_contents('productos.json'), true);
$cantidades = $_POST['cantidades'] ?? [];

if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

foreach ($cantidades as $id => $cantidad) {
    $id = (int)$id;
    $cantidad = (int)$cantidad;

    if ($cantidad > 0) {
        foreach ($productos as $producto) {
            if ($producto['id'] === $id) {
                if (!isset($_SESSION['carrito'][$id])) {
                    $_SESSION['carrito'][$id] = [
                        'nombre' => $producto['nombre'],
                        'precio' => $producto['precio'],
                        'cantidad' => $cantidad
                    ];
                } else {
                    $_SESSION['carrito'][$id]['cantidad'] += $cantidad;
                }
                break;
            }
        }
    }
}
header("Location: ver_carrito.php");
exit();
