<?php
// ===============================
// Sistema de Gestión de Inventario
// Basado en archivo JSON
// ===============================

// Función para leer el inventario desde el archivo JSON
function leerInventario($archivo) {
    if (!file_exists($archivo)) {
        echo "El archivo $archivo no existe.\n";
        return [];
    }
    $contenido = file_get_contents($archivo);
    return json_decode($contenido, true);
}

// Función para ordenar el inventario alfabéticamente por nombre
function ordenarInventario($inventario) {
    usort($inventario, function ($a, $b) {
        return strcmp($a['nombre'], $b['nombre']);
    });
    return $inventario;
}

// Función para mostrar un resumen del inventario
function mostrarInventario($inventario) {
    echo "===== Resumen del Inventario =====\n";
    foreach ($inventario as $producto) {
        echo "Producto: {$producto['nombre']} | Precio: \${$producto['precio']} | Cantidad: {$producto['cantidad']}\n";
    }
    echo "==================================\n";
}

// Función para calcular el valor total del inventario
function calcularValorTotal($inventario) {
    $total = array_sum(array_map(function ($producto) {
        return $producto['precio'] * $producto['cantidad'];
    }, $inventario));
    return $total;
}

// Función para generar un informe de productos con stock bajo (<5)
function informeStockBajo($inventario) {
    $stockBajo = array_filter($inventario, function ($producto) {
        return $producto['cantidad'] < 5;
    });
    echo "===== Informe de Stock Bajo =====\n";
    if (empty($stockBajo)) {
        echo "No hay productos con stock bajo.\n";
    } else {
        foreach ($stockBajo as $producto) {
            echo "Producto: {$producto['nombre']} | Cantidad: {$producto['cantidad']}\n";
        }
    }
    echo "=================================\n";
}

// ===============================
// Script Principal
// ===============================
$archivo = "inventario.json";
$inventario = leerInventario($archivo);

if (!empty($inventario)) {
    $inventario = ordenarInventario($inventario);
    mostrarInventario($inventario);

    $valorTotal = calcularValorTotal($inventario);
    echo "Valor total del inventario: \$$valorTotal\n";

    informeStockBajo($inventario);
} else {
    echo "No se pudo cargar el inventario.\n";
}
?>
